<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventDailyQr;
use App\Models\EventDailyAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\UserNotification;

class EventParticipationController extends Controller
{
    public function register(Event $event, Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Login dahulu untuk mendaftar.');
        }

        if (!(bool) ($event->is_published ?? false)) {
            return redirect()->back()->with('error', 'Event belum diterbitkan.');
        }

        // Cek apakah sudah terdaftar
        $registration = EventRegistration::where('event_id', $event->id)->where('user_id', $user->id)->first();
        if ($registration && $registration->status === 'active') {
            return redirect()->back()->with('info', 'Anda sudah terdaftar.');
        }
        $createdNewOrActivated = false;
        DB::transaction(function () use ($event, $user, &$registration, &$createdNewOrActivated) {
            if (!$registration) {
                $registration = new EventRegistration();
                $registration->event_id = $event->id;
                $registration->user_id = $user->id;
                $createdNewOrActivated = true;
            }
            if ($registration->status !== 'active') {
                $createdNewOrActivated = true;
            }
            $registration->status = 'active';
            if (empty($registration->registration_code)) {
                $registration->registration_code = 'EVT' . $event->id . '-' . Str::upper(Str::random(6));
            }
            $registration->save();
        });

        // Add points for event registration (only if newly created or activated)
        if ($createdNewOrActivated) {
            try {
                $pointsService = app(\App\Services\UserPointsService::class);
                $pointsService->addEventPoints($user, $event, $registration);
            } catch (\Throwable $e) {
                // Log error but don't block registration
                \Log::warning('Failed to add points for event registration', [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Create a user notification for successful free registration
        if ($createdNewOrActivated) {
            try {
                UserNotification::create([
                    'user_id' => $user->id,
                    'type' => 'event_registration',
                    'title' => 'Pendaftaran Dikonfirmasi',
                    'message' => 'Pendaftaran untuk "' . $event->title . '" telah dikonfirmasi.',
                    // Redirect back to detail event instead of ticket page
                    'data' => ['url' => route('events.registered.detail', $event)],
                    'expires_at' => now()->addDays(14),
                ]);
            } catch (\Throwable $e) { /* ignore notification errors */
            }
        }
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Anda berhasil terdaftar!', 'redirect' => route('events.registered.detail', $event)]);
        }
        return redirect()->back()->with('success', 'Anda berhasil terdaftar!');
    }

    public function submitFeedback(Event $event, Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Login diperlukan.');
        }
        $registration = EventRegistration::where('event_id', $event->id)->where('user_id', $user->id)->first();
        if (!$registration || $registration->status !== 'active') {
            return redirect()->back()->with('error', 'Anda belum terdaftar.');
        }
        // Pastikan event sudah selesai (robust parsing for time fields)
        $eventDate = $event->event_date ? Carbon::parse($event->event_date) : null;
        $parseEventTime = function ($date, $raw) {
            if (empty($raw))
                return null;
            if ($raw instanceof Carbon)
                return $raw;
            $s = trim((string) $raw);
            // Strip trailing timezone labels
            $s = preg_replace('/\s*(WIB|WITA|WIT)\s*$/i', '', $s);
            // Normalize 14.30 -> 14:30
            if (preg_match('/^\d{1,2}\.\d{2}$/', $s))
                $s = str_replace('.', ':', $s);
            // If already contains a date, parse directly
            if (preg_match('/\d{4}-\d{2}-\d{2}/', $s)) {
                try {
                    return Carbon::parse($s);
                } catch (\Throwable $e) {
                    return null;
                }
            }
            // Combine with provided date
            if ($date) {
                $dateStr = $date instanceof Carbon ? $date->format('Y-m-d') : (string) $date;
                try {
                    return Carbon::parse($dateStr . ' ' . $s);
                } catch (\Throwable $e) {
                    return null;
                }
            }
            try {
                return Carbon::parse($s);
            } catch (\Throwable $e) {
                return null;
            }
        };
        $endTime = $parseEventTime($eventDate, $event->event_time_end);
        if (!$endTime && $eventDate) {
            $endTime = $eventDate->copy()->endOfDay();
        }

        $isAttended = !empty($registration->attended_at) || in_array(strtolower((string) $registration->attendance_status), ['yes', 'present', 'attended']);

        if (!$isAttended && (!$endTime || Carbon::now()->lte($endTime))) {
            return redirect()->back()->with('error', 'Feedback dapat dikirim setelah Anda melakukan absensi atau setelah event selesai.');
        }
        $data = $request->validate([
            'feedback_text' => 'required|string|min:5',
        ]);
        $registration->feedback_text = $data['feedback_text'];
        $registration->feedback_submitted_at = Carbon::now();
        // Issue certificate immediately after feedback submitted
        if (empty($registration->certificate_issued_at)) {
            $registration->certificate_issued_at = Carbon::now();
        }
        $registration->save();

        // Add points for feedback
        try {
            $pointsService = app(\App\Services\UserPointsService::class);
            $pointsService->addFeedbackPoints($user);
        } catch (\Throwable $e) {
            \Log::warning('Failed to add points for feedback', [
                'user_id' => $user->id,
                'event_id' => $event->id,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->back()->with('success', 'Terima kasih atas feedback Anda! Sertifikat telah terbuka.');
    }

    public function submitAttendance(Event $event, Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Login diperlukan.');
        }
        $registration = EventRegistration::where('event_id', $event->id)->where('user_id', $user->id)->first();
        if (!$registration || $registration->status !== 'active') {
            return redirect()->back()->with('error', 'Anda belum terdaftar.');
        }
        // Pastikan event sudah selesai
        $eventDate = $event->event_date ? Carbon::parse($event->event_date) : null;
        $parseEventTime = function ($date, $raw) {
            if (empty($raw))
                return null;
            if ($raw instanceof Carbon)
                return $raw;
            $s = trim((string) $raw);
            $s = preg_replace('/\s*(WIB|WITA|WIT)\s*$/i', '', $s);
            if (preg_match('/^\d{1,2}\.\d{2}$/', $s))
                $s = str_replace('.', ':', $s);
            if (preg_match('/\d{4}-\d{2}-\d{2}/', $s)) {
                try {
                    return Carbon::parse($s);
                } catch (\Throwable $e) {
                    return null;
                }
            }
            if ($date) {
                $dateStr = $date instanceof Carbon ? $date->format('Y-m-d') : (string) $date;
                try {
                    return Carbon::parse($dateStr . ' ' . $s);
                } catch (\Throwable $e) {
                    return null;
                }
            }
            try {
                return Carbon::parse($s);
            } catch (\Throwable $e) {
                return null;
            }
        };
        $endTime = $parseEventTime($eventDate, $event->event_time_end);
        if (!$endTime && $eventDate) {
            $endTime = $eventDate->copy()->endOfDay();
        }
        if (!$endTime || Carbon::now()->lte($endTime)) {
            return redirect()->back()->with('error', 'Event belum selesai, attendance belum dapat dikirim.');
        }
        // Fallback: attended yes/no for non-code flows
        $data = $request->validate([
            'attended' => 'required|in:yes,no',
        ]);
        $registration->attendance_status = $data['attended'];
        $registration->attended_at = Carbon::now();
        $registration->attendance_scan_qr = Carbon::now();
        $registration->save();
        return redirect()->back()->with('success', 'Attendance berhasil disimpan.');
    }

    /**
     * Persist attendance upon scanning the event QR.
     *
     * Supports two modes:
     * 1. Multi-day events: validates per-day token from event_daily_qrs.
     * 2. Single-day (legacy): validates against events.attendance_qr_token.
     *
     * Records one daily attendance row per user per day.
     * Also marks the top-level registration as attended (attended_at / attendance_scan_qr)
     * on the first successful scan.
     */
    public function scanAttendance(Event $event, Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Login diperlukan.'], 401);
        }

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$registration || $registration->status !== 'active') {
            return response()->json(['message' => 'Anda belum terdaftar aktif.'], 403);
        }

        // Gate: event must have started
        $startAt = $event->start_at;
        $now = Carbon::now(config('app.timezone'));

        if ($startAt && $now->lt($startAt)) {
            return response()->json(['message' => 'Event belum dimulai.'], 422);
        }

        $data = $request->validate([
            'qr_text' => 'required|string|max:2048',
        ]);

        $text = trim($data['qr_text']);

        // ── Extract token and optional date from QR content ──────────────────
        $token = null;
        $qrDate = null;

        if (preg_match('/[?&]t=([0-9a-f]{16,})/i', $text, $m)) {
            $token = $m[1];
        } elseif (preg_match('/^[0-9a-f]{16,}$/i', $text)) {
            $token = $text;
        }

        // Extract optional date param ?d=YYYY-MM-DD
        if (preg_match('/[?&]d=(\d{4}-\d{2}-\d{2})/i', $text, $md)) {
            $qrDate = $md[1];
        }

        $today = $now->format('Y-m-d');

        // ── Try per-day QR validation first ──────────────────────────────────
        $isMultiDay = EventDailyQr::where('event_id', $event->id)->count() > 1;
        $dailyQr = null;

        if ($token) {
            // Look up by token in daily QR table
            $dailyQr = EventDailyQr::where('event_id', $event->id)
                ->where('token', $token)
                ->first();
        }

        if ($dailyQr) {
            // Validate: the daily QR must match today's date
            $qrDayDate = $dailyQr->qr_date instanceof Carbon
                ? $dailyQr->qr_date->format('Y-m-d')
                : (string) $dailyQr->qr_date;

            if ($qrDayDate !== $today) {
                return response()->json([
                    'message' => 'QR tidak valid. Harap scan QR yang telah dibagikan oleh Panitia Penyelenggara',
                ], 422);
            }

            // Check if user already scanned today
            $alreadyScanned = EventDailyAttendance::where('event_registration_id', $registration->id)
                ->where('attendance_date', $today)
                ->exists();

            if ($alreadyScanned) {
                return response()->json(['message' => 'Attendance hari ini sudah tercatat.'], 200);
            }

            // Record daily attendance
            DB::transaction(function () use ($registration, $dailyQr, $today, $now) {
                EventDailyAttendance::create([
                    'event_registration_id' => $registration->id,
                    'event_daily_qr_id' => $dailyQr->id,
                    'attendance_date' => $today,
                    'day_number' => $dailyQr->day_number,
                    'scanned_at' => $now,
                ]);

                // Mark top-level registration attended on first scan
                if (empty($registration->attended_at)) {
                    $registration->attendance_status = 'yes';
                    $registration->attended_at = $now;
                    $registration->attendance_scan_qr = $now;
                    $registration->save();
                }
            });

            return response()->json([
                'message' => 'Attendance Hari ke-' . $dailyQr->day_number . ' berhasil disimpan.',
                'day_number' => $dailyQr->day_number,
                'day_date' => $qrDayDate,
            ], 200);
        }

        // If it is a multi-day event but no valid daily QR matched (e.g. wrong token, no token, or invalid QR)
        if ($isMultiDay) {
            return response()->json([
                'message' => 'QR tidak valid. Harap scan QR yang telah dibagikan oleh Panitia Penyelenggara',
            ], 422);
        }

        // ── Single-day validation ────────────────────────────────────
        $tokenOk = !empty($event->attendance_qr_token) && $token
            && hash_equals((string) $event->attendance_qr_token, (string) $token);

        if (!empty($event->attendance_qr_token)) {
            if (!$tokenOk) {
                return response()->json(['message' => 'QR tidak valid. Harap scan QR yang telah dibagikan oleh Panitia Penyelenggara'], 422);
            }
        } else {
            // Fallback to event URL only if no token is set at all for this event
            $eventUrlPattern = sprintf('/\/events\/%d(\?|$)/', (int) $event->id);
            $urlOk = (bool) preg_match($eventUrlPattern, $text);
            if (!$urlOk) {
                return response()->json(['message' => 'QR tidak valid. Harap scan QR yang telah dibagikan oleh Panitia Penyelenggara'], 422);
            }
        }

        if (!empty($registration->attendance_scan_qr) || !empty($registration->attended_at)) {
            return response()->json(['message' => 'Attendance sudah tercatat.'], 200);
        }

        $registration->attendance_status = 'yes';
        $registration->attended_at = $now;
        $registration->attendance_scan_qr = $now;
        $registration->save();

        return response()->json(['message' => 'Attendance berhasil disimpan.'], 200);
    }
}
