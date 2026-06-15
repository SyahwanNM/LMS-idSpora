<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventDailyQr;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Storage;

class EventDailyQrService
{
    /**
     * Return all event dates (Carbon instances) spanning event_date → event_until_date.
     * Falls back to a single day if event_until_date is not set.
     *
     * @return Carbon[]
     */
    public function getEventDates(Event $event): array
    {
        $start = $event->event_date instanceof Carbon
            ? $event->event_date->copy()->startOfDay()
            : Carbon::parse($event->event_date)->startOfDay();

        $end = !empty($event->event_until_date)
            ? ($event->event_until_date instanceof Carbon
                ? $event->event_until_date->copy()->startOfDay()
                : Carbon::parse($event->event_until_date)->startOfDay())
            : $start->copy();

        // Safety cap: max 30 days
        if ($end->diffInDays($start) > 29) {
            $end = $start->copy()->addDays(29);
        }

        $period = CarbonPeriod::create($start, '1 day', $end);
        return array_values(iterator_to_array($period));
    }

    /**
     * Ensure all per-day QR rows exist for this event.
     * Creates missing rows and generates QR images; does NOT overwrite existing ones.
     * Does NOT remove out-of-range rows — use syncDailyQrs() when dates may have changed.
     *
     * @return EventDailyQr[]
     */
    public function ensureAllDailyQrs(Event $event): array
    {
        return $this->syncDailyQrs($event, deleteOutOfRange: false);
    }

    /**
     * Synchronise per-day QR rows to exactly match the current event date range.
     * - Deletes rows whose qr_date is outside the new range (and their image files).
     * - Re-numbers remaining rows so day_number stays 1-based and contiguous.
     * - Creates + generates any missing rows for dates in range.
     *
     * @return EventDailyQr[]
     */
    public function syncDailyQrs(Event $event, bool $deleteOutOfRange = true): array
    {
        if ($event->jenis === 'Lomba') {
            $toDelete = EventDailyQr::where('event_id', $event->id)->get();
            foreach ($toDelete as $old) {
                if (!empty($old->qr_image)) {
                    try { Storage::disk('public')->delete($old->qr_image); } catch (\Throwable $e) {}
                }
                $old->delete();
            }
            return [];
        }

        $dates      = $this->getEventDates($event);
        $validDates = array_map(fn($d) => $d->format('Y-m-d'), $dates);

        if ($deleteOutOfRange) {
            $toDelete = EventDailyQr::where('event_id', $event->id)
                ->whereNotIn('qr_date', $validDates)
                ->get();

            foreach ($toDelete as $old) {
                if (!empty($old->qr_image)) {
                    try { Storage::disk('public')->delete($old->qr_image); } catch (\Throwable $e) {}
                }
                $old->delete();
            }
        }

        $qrs = [];

        foreach ($dates as $dayIndex => $date) {
            $qrDate    = $date->format('Y-m-d');
            $dayNumber = $dayIndex + 1;

            $dailyQr = EventDailyQr::firstOrCreate(
                ['event_id' => $event->id, 'qr_date' => $qrDate],
                ['day_number' => $dayNumber, 'token' => bin2hex(random_bytes(16))]
            );

            // Keep day_number in sync if dates were reordered
            if ($dailyQr->day_number !== $dayNumber) {
                $dailyQr->day_number = $dayNumber;
                $dailyQr->save();
            }

            // Generate image if missing
            if (empty($dailyQr->qr_image)) {
                $this->generateImage($dailyQr, $event);
            }

            $qrs[] = $dailyQr->fresh();
        }

        return $qrs;
    }

    /**
     * Force-regenerate the QR image for a specific day (creates new token too).
     */
    public function regenerateDailyQr(EventDailyQr $dailyQr, Event $event): EventDailyQr
    {
        // Delete old image
        if (!empty($dailyQr->qr_image)) {
            try { Storage::disk('public')->delete($dailyQr->qr_image); } catch (\Throwable $e) {}
        }

        $dailyQr->token    = bin2hex(random_bytes(16));
        $dailyQr->qr_image = null;
        $dailyQr->save();

        $this->generateImage($dailyQr, $event);
        return $dailyQr->fresh();
    }

    /**
     * Get (or create) the QR for today's date.
     * Returns null if today is not within the event date range.
     */
    public function getTodayQr(Event $event): ?EventDailyQr
    {
        if ($event->jenis === 'Lomba') {
            return null;
        }

        $today = Carbon::now(config('app.timezone'))->startOfDay();
        $dates = $this->getEventDates($event);

        $todayDateStr = $today->format('Y-m-d');
        $matchIndex   = null;

        foreach ($dates as $i => $date) {
            if ($date->format('Y-m-d') === $todayDateStr) {
                $matchIndex = $i;
                break;
            }
        }

        if ($matchIndex === null) {
            return null; // today is not an event day
        }

        $dayNumber = $matchIndex + 1;
        $dailyQr   = EventDailyQr::firstOrCreate(
            ['event_id' => $event->id, 'qr_date' => $todayDateStr],
            ['day_number' => $dayNumber, 'token' => bin2hex(random_bytes(16))]
        );

        if (empty($dailyQr->qr_image)) {
            $this->generateImage($dailyQr, $event);
            $dailyQr->refresh();
        }

        return $dailyQr;
    }

    /**
     * Generate and persist the QR image for a daily QR row.
     */
    public function generateImage(EventDailyQr $dailyQr, Event $event): void
    {
        try {
            // Normalise qr_date to plain Y-m-d string (guard against Carbon/datetime cast)
            $dateStr  = $dailyQr->qr_date instanceof \Carbon\Carbon
                ? $dailyQr->qr_date->format('Y-m-d')
                : \Carbon\Carbon::parse((string) $dailyQr->qr_date)->format('Y-m-d');

            // Embed event_id + date + token so the scanner can validate per-day
            $content  = url('/events/' . $event->id . '?t=' . $dailyQr->token . '&d=' . $dateStr);
            $filename = 'events/qr/event-' . $event->id . '-day' . $dailyQr->day_number . '-' . $dateStr . '.png';

            $png = null;
            try {
                if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                    $png = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(600)->margin(1)->generate($content);
                }
            } catch (\Throwable $e) {}

            if ($png) {
                Storage::disk('public')->put($filename, $png);
            } else {
                // SVG fallback
                $svg = null;
                try {
                    if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(600)->margin(1)->generate($content);
                    }
                } catch (\Throwable $e) {}

                if ($svg) {
                    $filename = str_replace('.png', '.svg', $filename);
                    Storage::disk('public')->put($filename, $svg);
                } else {
                    // Minimal placeholder PNG (1x1 transparent)
                    $placeholder = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/58BAgMDAv8x2WQAAAAASUVORK5CYII=');
                    Storage::disk('public')->put($filename, $placeholder);
                }
            }

            $dailyQr->qr_image = $filename;
            $dailyQr->save();
        } catch (\Throwable $e) {
            \Log::warning('EventDailyQrService: failed to generate QR image', [
                'event_id'   => $event->id,
                'daily_qr_id' => $dailyQr->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
