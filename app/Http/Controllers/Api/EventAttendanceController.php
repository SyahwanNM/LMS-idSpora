<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventAttendanceController extends Controller
{
    /**
     * GET /api/events/{event}/attendance/status
     * Returns the authenticated user's attendance status for an event.
     */
    public function status(Request $request, Event $event)
    {
        $user = $request->user();

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$registration || $registration->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak terdaftar aktif pada event ini.',
            ], 403);
        }

        $attended = !empty($registration->attended_at) || !empty($registration->attendance_scan_qr);

        return response()->json([
            'status'  => 'success',
            'message' => 'Status kehadiran',
            'data'    => [
                'event_id'            => $event->id,
                'event_title'         => $event->title,
                'attendance_status'   => $registration->attendance_status ?? 'no',
                'attended'            => $attended,
                'attended_at'         => $registration->attended_at?->toISOString(),
                'registration_code'   => $registration->registration_code,
            ],
        ]);
    }

    /**
     * POST /api/events/{event}/attendance/scan
     * Submit QR scan result to record attendance.
     *
     * Body: { "qr_text": "<decoded QR string>" }
     */
    public function scan(Request $request, Event $event)
    {
        $user = $request->user();

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$registration || $registration->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak terdaftar aktif pada event ini.',
            ], 403);
        }

        // Event must have started
        $startAt = $event->start_at;
        if ($startAt && Carbon::now()->lt($startAt)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Event belum dimulai.',
            ], 422);
        }

        $validated = $request->validate([
            'qr_text' => 'required|string|max:2048',
        ]);

        // Already attended
        if (!empty($registration->attended_at) || !empty($registration->attendance_scan_qr)) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Kehadiran sudah tercatat sebelumnya.',
                'data'    => ['attended_at' => $registration->attended_at?->toISOString()],
            ]);
        }

        // Validate QR token
        $text  = trim($validated['qr_text']);
        $token = null;

        if (preg_match('/[?&]t=([0-9a-f]{16,})/i', $text, $m)) {
            $token = $m[1];
        } elseif (preg_match('/^[0-9a-f]{16,}$/i', $text)) {
            $token = $text;
        }

        $today = Carbon::now(config('app.timezone'))->format('Y-m-d');
        $isMultiDay = \App\Models\EventDailyQr::where('event_id', $event->id)->count() > 1;
        $dailyQr = null;

        if ($token) {
            $dailyQr = \App\Models\EventDailyQr::where('event_id', $event->id)
                ->where('token', $token)
                ->first();
        }

        if ($dailyQr) {
            $qrDayDate = $dailyQr->qr_date instanceof Carbon
                ? $dailyQr->qr_date->format('Y-m-d')
                : (string) $dailyQr->qr_date;

            if ($qrDayDate !== $today) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'QR tidak valid. Harap scan QR yang telah dibagikan oleh Panitia Penyelenggara',
                ], 422);
            }

            $alreadyScanned = \App\Models\EventDailyAttendance::where('event_registration_id', $registration->id)
                ->where('attendance_date', $today)
                ->exists();

            if ($alreadyScanned) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Kehadiran sudah tercatat sebelumnya.',
                    'data'    => ['attended_at' => $registration->attended_at?->toISOString()],
                ]);
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($registration, $dailyQr, $today) {
                \App\Models\EventDailyAttendance::create([
                    'event_registration_id' => $registration->id,
                    'event_daily_qr_id' => $dailyQr->id,
                    'attendance_date' => $today,
                    'day_number' => $dailyQr->day_number,
                    'scanned_at' => Carbon::now(),
                ]);

                if (empty($registration->attended_at)) {
                    $registration->attendance_status = 'yes';
                    $registration->attended_at = Carbon::now();
                    $registration->attendance_scan_qr = Carbon::now();
                    $registration->save();
                }
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Kehadiran berhasil dicatat.',
                'data'    => [
                    'attended_at'       => $registration->fresh()->attended_at->toISOString(),
                    'attendance_status' => $registration->attendance_status,
                ],
            ]);
        }

        if ($isMultiDay) {
            return response()->json([
                'status'  => 'error',
                'message' => 'QR tidak valid. Harap scan QR yang telah dibagikan oleh Panitia Penyelenggara',
            ], 422);
        }

        $tokenOk = !empty($event->attendance_qr_token) && $token
            && hash_equals((string) $event->attendance_qr_token, (string) $token);

        if (!empty($event->attendance_qr_token)) {
            if (!$tokenOk) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'QR tidak valid. Harap scan QR yang telah dibagikan oleh Panitia Penyelenggara',
                ], 422);
            }
        } else {
            $urlOk = (bool) preg_match(sprintf('/\/events\/%d(\?|$)/', (int) $event->id), $text);
            if (!$urlOk) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'QR tidak valid. Harap scan QR yang telah dibagikan oleh Panitia Penyelenggara',
                ], 422);
            }
        }

        $now = Carbon::now();
        $registration->attendance_status  = 'yes';
        $registration->attended_at        = $now;
        $registration->attendance_scan_qr = $now;
        $registration->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Kehadiran berhasil dicatat.',
            'data'    => [
                'attended_at'       => $registration->attended_at->toISOString(),
                'attendance_status' => $registration->attendance_status,
            ],
        ]);
    }

    /**
     * GET /api/events/{event}/attendance/qr-info
     * Returns QR image URL for the event (if available) so the mobile app
     * can display it for scanning by the organizer's scanner.
     */
    public function qrInfo(Request $request, Event $event)
    {
        $user = $request->user();

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$registration || $registration->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak terdaftar aktif pada event ini.',
            ], 403);
        }

        $hasQr = !empty($event->attendance_qr_token) || !empty($event->attendance_qr_image);

        return response()->json([
            'status'  => 'success',
            'message' => 'Info QR kehadiran',
            'data'    => [
                'event_id'          => $event->id,
                'event_title'       => $event->title,
                'event_date'        => $event->event_date?->toDateString(),
                'event_start'       => $event->start_at?->toISOString(),
                'qr_available'      => $hasQr,
                'qr_image_url'      => $event->attendance_qr_image_url,
                'scan_endpoint'     => route('api.events.attendance.scan', $event->id),
            ],
        ]);
    }
}
