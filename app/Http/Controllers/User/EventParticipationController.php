<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventDailyQr;
use App\Models\EventDailyAttendance;
use App\Models\ManualPayment;
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

        if (strtolower(trim($event->jenis ?? '')) === 'lomba' && $event->until_submission && \Carbon\Carbon::now()->gt($event->until_submission)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Pendaftaran Lomba sudah ditutup.'], 422);
            }
            return redirect()->back()->with('error', 'Pendaftaran Lomba sudah ditutup.');
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
                    'title' => 'Registration Confirmed',
                    'message' => 'Registration for "' . $event->title . '" has been confirmed.',
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
        $isLomba = strtolower(trim($event->jenis ?? '')) === 'lomba';
        if ($isLomba) {
            $endTime = $event->until_submission_2 ? Carbon::parse($event->until_submission_2) : (
                $event->until_submission ? Carbon::parse($event->until_submission) : null
            );
        } else {
            $endTime = $parseEventTime($eventDate, $event->event_time_end);
            if (!$endTime && $eventDate) {
                $endTime = $eventDate->copy()->endOfDay();
            }
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
        if ($event->jenis === 'Lomba') {
            return response()->json(['message' => 'Lomba tidak memiliki QR Attendance.'], 403);
        }

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

    public function uploadInitialSubmission(Event $event, Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Login diperlukan.');
        }

        $registration = EventRegistration::where('event_id', $event->id)->where('user_id', $user->id)->first();
        if (!$registration || $registration->status !== 'active') {
            return redirect()->back()->with('error', 'Anda belum terdaftar aktif.');
        }

        if ($registration->team_id && !$registration->is_team_leader) {
            return redirect()->back()->with('error', 'Hanya Ketua Tim yang dapat mengunggah submission.');
        }

        if ($registration->submission_status === 'tidak_lolos') {
            return redirect()->back()->with('error', 'Anda tidak dapat memperbarui submission karena dinyatakan tidak lolos.');
        }

        if ($registration->submission_status === 'lolos') {
            return redirect()->back()->with('error', 'Anda tidak dapat memperbarui submission karena telah dinyatakan lolos ke tahap berikutnya.');
        }

        $now = Carbon::now();
        if ($event->start_submission && $now->lt($event->start_submission)) {
            return redirect()->back()->with('error', 'Pengiriman submission belum dibuka.');
        }
        if ($event->until_submission && $now->gt($event->until_submission)) {
            return redirect()->back()->with('error', 'Pengiriman submission sudah ditutup.');
        }

        $request->validate([
            'submission_file' => 'required|file|mimes:pdf|max:10240', // 10 MB
        ]);

        if ($request->hasFile('submission_file')) {
            if ($registration->submission_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($registration->submission_path);
            }

            $path = $request->file('submission_file')->store('submissions', 'public');
            $registration->submission_path = $path;
            $registration->submission_uploaded_at = $now;
            $registration->submission_status = 'pending';
            $registration->save();

            if ($registration->team_id) {
                EventRegistration::where('team_id', $registration->team_id)
                    ->where('id', '!=', $registration->id)
                    ->update([
                        'submission_path' => $path,
                        'submission_uploaded_at' => $now,
                        'submission_status' => 'pending',
                    ]);
            }

            return redirect()->back()->with('success', 'Initial submission successfully uploaded.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah file.');
    }

    public function uploadSecondSubmission(Event $event, Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Login diperlukan.');
        }

        $registration = EventRegistration::where('event_id', $event->id)->where('user_id', $user->id)->first();
        if (!$registration || $registration->status !== 'active') {
            return redirect()->back()->with('error', 'Anda belum terdaftar aktif.');
        }

        if ($registration->team_id && !$registration->is_team_leader) {
            return redirect()->back()->with('error', 'Hanya Ketua Tim yang dapat mengunggah submission.');
        }

        if ($registration->submission_status !== 'lolos') {
            return redirect()->back()->with('error', 'Anda tidak lolos ke tahap berikutnya.');
        }

        // Block stage 2 upload if payment is still pending
        if ($registration->stage2_payment_status === 'pending') {
            return redirect()->route('events.payment.stage2', $event)
                ->with('error', 'Harap selesaikan pembayaran Tahap 2 terlebih dahulu sebelum mengunggah submission.');
        }

        $now = Carbon::now();
        if ($event->announcement_date && $now->lt($event->announcement_date)) {
            return redirect()->back()->with('error', 'Pengiriman submission kedua belum dibuka.');
        }
        if ($event->until_submission_2 && $now->gt($event->until_submission_2)) {
            return redirect()->back()->with('error', 'Pengiriman submission kedua sudah ditutup.');
        }

        $request->validate([
            'submission_file_2' => 'required|file|mimes:pdf|max:10240', // 10 MB
        ]);

        if ($request->hasFile('submission_file_2')) {
            if ($registration->submission_path_2) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($registration->submission_path_2);
            }

            $path = $request->file('submission_file_2')->store('submissions', 'public');
            $registration->submission_path_2 = $path;
            $registration->submission_2_uploaded_at = $now;
            $registration->save();

            if ($registration->team_id) {
                EventRegistration::where('team_id', $registration->team_id)
                    ->where('id', '!=', $registration->id)
                    ->update([
                        'submission_path_2' => $path,
                        'submission_2_uploaded_at' => $now,
                    ]);
            }

            return redirect()->back()->with('success', 'Second submission successfully uploaded.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah file.');
    }

    // ─────────────────────────── Stage 2 Payment ────────────────────────────

    /**
     * Show Stage 2 payment page.
     */
    public function showStage2Payment(Event $event)
    {
        $user = Auth::user();
        if (!$user)
            return redirect()->route('login');

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)->first();

        if (!$registration || $registration->submission_status !== 'lolos') {
            abort(403, 'Halaman ini hanya untuk peserta yang lolos Tahap 1.');
        }

        $now = \Carbon\Carbon::now(config('app.timezone'));
        if ($event->finalist_payment_start && $now->lt($event->finalist_payment_start)) {
            return redirect()->route('events.registered.detail', $event)
                ->with('error', 'Stage 2 Payment / Finalist Registration is not open yet. It will open on ' . $event->finalist_payment_start->translatedFormat('d M Y, H:i') . ' WIB.');
        }

        if ($event->finalist_payment_end && $now->gt($event->finalist_payment_end)) {
            return redirect()->route('events.registered.detail', $event)
                ->with('error', 'Stage 2 Payment / Finalist Registration is closed. It ended on ' . $event->finalist_payment_end->translatedFormat('d M Y, H:i') . ' WIB.');
        }

        // Already paid or not required
        if (in_array($registration->stage2_payment_status, ['settled', 'not_required'], true)) {
            return redirect()->route('events.registered.detail', $event)
                ->with('info', 'Pembayaran tahap 2 sudah selesai atau tidak diperlukan.');
        }

        // Check for existing pending manual payment
        $existingPayment = ManualPayment::where('event_registration_id', $registration->id)
            ->whereJsonContains('metadata->stage', 2)
            ->where('status', 'pending')
            ->latest()->first();

        $isStage2 = true;
        return view('user.payment', compact('event', 'registration', 'existingPayment', 'isStage2'));
    }

    /**
     * Submit Stage 2 manual transfer payment.
     */
    public function submitStage2ManualPayment(Event $event, Request $request)
    {
        $user = Auth::user();
        if (!$user)
            return redirect()->route('login');

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)->first();

        if (!$registration || $registration->submission_status !== 'lolos') {
            abort(403);
        }

        $now = \Carbon\Carbon::now(config('app.timezone'));
        if ($event->finalist_payment_start && $now->lt($event->finalist_payment_start)) {
            return redirect()->route('events.registered.detail', $event)
                ->with('error', 'Stage 2 Payment / Finalist Registration is not open yet. It will open on ' . $event->finalist_payment_start->translatedFormat('d M Y, H:i') . ' WIB.');
        }

        if ($event->finalist_payment_end && $now->gt($event->finalist_payment_end)) {
            return redirect()->route('events.registered.detail', $event)
                ->with('error', 'Stage 2 Payment / Finalist Registration is closed. It ended on ' . $event->finalist_payment_end->translatedFormat('d M Y, H:i') . ' WIB.');
        }

        if ($registration->stage2_payment_status !== 'pending') {
            return redirect()->back()->with('info', 'Status pembayaran sudah diperbarui.');
        }

        $stage2Price = (float) ($event->price_stage2 ?? 0);
        if ($stage2Price <= 0) {
            $registration->stage2_payment_status = 'settled';
            $registration->stage2_payment_at = now();
            $registration->save();

            return redirect()->route('events.registered.detail', $event)
                ->with('success', 'Registrasi Tahap 2 berhasil diselesaikan!');
        }

        $request->validate([
            'whatsapp' => 'required|string|max:30',
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $orderId = 'STG2-' . $registration->id . '-' . strtoupper(Str::random(6));

        $payment = ManualPayment::create([
            'event_id' => $event->id,
            'event_registration_id' => $registration->id,
            'user_id' => $user->id,
            'order_id' => $orderId,
            'amount' => (float) ($event->price_stage2 ?? 0),
            'currency' => 'IDR',
            'method' => 'manual_transfer',
            'whatsapp_number' => $request->whatsapp,
            'status' => 'pending',
            'metadata' => ['stage' => 2, 'source' => 'stage2_payment'],
        ]);

        // Upload proof
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $path = $file->store('payments/stage2', 'public');
            \App\Models\PaymentProof::create([
                'manual_payment_id' => $payment->id,
                'event_registration_id' => $registration->id,
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $user->id,
            ]);
        }

        return redirect()->route('events.registered.detail', $event)
            ->with('success', 'Bukti pembayaran berhasil dikirim. Mohon tunggu konfirmasi admin.');
    }

    /**
     * Create Midtrans order for Stage 2 payment.
     */
    public function createStage2MidtransOrder(Event $event, Request $request)
    {
        $user = Auth::user();
        if (!$user)
            return response()->json(['error' => 'Unauthenticated'], 401);

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)->first();

        if (!$registration || $registration->submission_status !== 'lolos') {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $now = \Carbon\Carbon::now(config('app.timezone'));
        if ($event->finalist_payment_start && $now->lt($event->finalist_payment_start)) {
            return response()->json(['error' => 'Stage 2 Payment / Finalist Registration is not open yet. It will open on ' . $event->finalist_payment_start->translatedFormat('d M Y, H:i') . ' WIB.'], 422);
        }

        if ($event->finalist_payment_end && $now->gt($event->finalist_payment_end)) {
            return response()->json(['error' => 'Stage 2 Payment / Finalist Registration is closed. It ended on ' . $event->finalist_payment_end->translatedFormat('d M Y, H:i') . ' WIB.'], 422);
        }

        if ($registration->stage2_payment_status !== 'pending') {
            return response()->json(['error' => 'Status pembayaran tidak memerlukan pembayaran.'], 422);
        }

        $amount = (float) ($event->price_stage2 ?? 0);
        if ($amount <= 0) {
            return response()->json(['error' => 'Nominal tidak valid.'], 422);
        }

        $forceNew = (bool) $request->boolean('force_new');

        // Reuse existing pending midtrans order if any
        $existingPayment = ManualPayment::where('event_registration_id', $registration->id)
            ->where('user_id', $user->id)
            ->where('method', 'midtrans')
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereJsonContains('metadata->stage', 2)
                    ->orWhere('order_id', 'like', 'STG2-%');
            })
            ->latest('id')
            ->first();

        if ($existingPayment && !$forceNew) {
            $snapToken = data_get($existingPayment->metadata, 'snap_token');
            if ($snapToken && $existingPayment->created_at && now()->diffInHours($existingPayment->created_at) < 24) {
                return response()->json(['snap_token' => $snapToken, 'order_id' => $existingPayment->order_id]);
            }
        }

        if ($existingPayment) {
            $existingPayment->update(['status' => 'expired']);
        }

        $orderId = 'STG2-' . $registration->id . '-' . strtoupper(Str::random(6));

        $dial = trim((string) $request->input('dial_code'));
        $wa = trim((string) $request->input('whatsapp'));
        $phone = trim($dial . $wa);
        if ($phone === '') {
            $phone = (string) ($user->phone ?? '');
        }

        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $amount,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $phone,
                ],
                'item_details' => [
                    [
                        'id' => 'stage2-' . $event->id,
                        'price' => (int) $amount,
                        'quantity' => 1,
                        'name' => 'Pembayaran Tahap 2 - ' . $event->title,
                    ]
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Record pending midtrans payment
            ManualPayment::create([
                'event_id' => $event->id,
                'event_registration_id' => $registration->id,
                'user_id' => $user->id,
                'order_id' => $orderId,
                'amount' => $amount,
                'currency' => 'IDR',
                'method' => 'midtrans',
                'status' => 'pending',
                'whatsapp_number' => $phone ?: null,
                'metadata' => [
                    'stage' => 2, 
                    'snap_token' => $snapToken, 
                    'snap_token_created_at' => now()->toIso8601String(),
                    'source' => 'stage2_payment'
                ],
            ]);

            return response()->json(['snap_token' => $snapToken, 'order_id' => $orderId]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Gagal membuat transaksi Midtrans: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Check pending Midtrans order status for Stage 2.
     */
    public function stage2PendingOrder(Event $event, Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)->first();

        if (!$registration) {
            return response()->json(['error' => 'Registration not found.'], 404);
        }

        $payment = ManualPayment::where('event_registration_id', $registration->id)
            ->where('user_id', $user->id)
            ->where('method', 'midtrans')
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereJsonContains('metadata->stage', 2)
                    ->orWhere('order_id', 'like', 'STG2-%');
            })
            ->latest('id')
            ->first();

        if ($payment && $payment->order_id) {
            try {
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = config('midtrans.is_production', false);

                $midtransStatus = (array) \Midtrans\Transaction::status($payment->order_id);

                $transactionStatus = $midtransStatus['transaction_status'] ?? null;
                $fraudStatus = $midtransStatus['fraud_status'] ?? null;

                $actualStatus = 'pending';
                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'challenge') {
                        $actualStatus = 'pending';
                    } else if ($fraudStatus == 'accept') {
                        $actualStatus = 'settled';
                    }
                } else if ($transactionStatus == 'settlement') {
                    $actualStatus = 'settled';
                } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                    $actualStatus = 'expired';
                }

                if ($actualStatus !== 'pending') {
                    $payment->status = $actualStatus;
                    $payment->save();

                    if ($actualStatus === 'settled') {
                        $registration->stage2_payment_status = 'settled';
                        $registration->stage2_payment_at = now();
                        $registration->save();
                    } else {
                        $registration->stage2_payment_status = 'pending';
                        $registration->save();
                    }

                    $payment = null;
                }
            } catch (\Throwable $e) {
                if (str_contains($e->getMessage(), '404') || str_contains(strtolower($e->getMessage()), 'not found')) {
                    $tokenCreatedAt = data_get($payment->metadata, 'snap_token_created_at') ?: $payment->created_at;
                    $tokenAgeMinutes = $tokenCreatedAt
                        ? abs(now()->diffInMinutes(\Carbon\Carbon::parse($tokenCreatedAt)))
                        : 0;

                    if ($tokenAgeMinutes >= 5) {
                        $payment->status = 'expired';
                        $payment->save();
                        $payment = null;
                    }
                }
            }
        }

        if ($payment) {
            return response()->json([
                'pending' => true,
                'order_id' => $payment->order_id,
                'snap_token' => data_get($payment->metadata, 'snap_token'),
                'whatsapp_number' => $payment->whatsapp_number,
            ]);
        }

        return response()->json([
            'pending' => false,
            'needs_force_new' => true,
        ]);
    }


    /**
     * Settle Stage 2 payment (called after Midtrans success callback / admin confirm).
     */
    public function settleStage2Payment(Event $event, Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        }

        $request->validate(['order_id' => 'required|string']);

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)->first();

        if (!$registration) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Registration not found.'], 404);
            }
            abort(404);
        }

        // Find the matching manual payment
        $payment = ManualPayment::where('order_id', $request->order_id)
            ->where('event_registration_id', $registration->id)
            ->whereJsonContains('metadata->stage', 2)
            ->first();

        if (!$payment) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Payment not found.'], 404);
            }
            abort(404);
        }

        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production', false);

            try {
                $status = (array) \Midtrans\Transaction::status($request->order_id);
                $transactionStatus = $status['transaction_status'] ?? null;
                $fraudStatus = $status['fraud_status'] ?? null;

                $actualStatus = 'pending';
                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'challenge') {
                        $actualStatus = 'pending';
                    } else if ($fraudStatus == 'accept') {
                        $actualStatus = 'settled';
                    }
                } else if ($transactionStatus == 'settlement') {
                    $actualStatus = 'settled';
                } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                    $actualStatus = 'expired';
                }
            } catch (\Throwable $statusException) {
                $is404 = str_contains($statusException->getMessage(), '404')
                    || str_contains(strtolower($statusException->getMessage()), 'not found');

                if ($is404) {
                    $tokenCreatedAt = data_get($payment->metadata, 'snap_token_created_at') ?: $payment->created_at;
                    $tokenAgeMinutes = $tokenCreatedAt
                        ? abs(now()->diffInMinutes(\Carbon\Carbon::parse($tokenCreatedAt)))
                        : 0;

                    if ($tokenAgeMinutes >= 5) {
                        $actualStatus = 'expired';
                    } else {
                        $actualStatus = 'pending';
                    }
                } else {
                    throw $statusException;
                }
            }

            $payment->status = $actualStatus;
            $payment->save();

            if ($actualStatus === 'settled') {
                $registration->stage2_payment_status = 'settled';
                $registration->stage2_payment_at = now();
                $registration->save();
            } else {
                $registration->stage2_payment_status = 'pending';
                $registration->save();
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => $actualStatus === 'settled',
                    'status' => $actualStatus,
                    'message' => $actualStatus === 'settled'
                        ? 'Pembayaran Tahap 2 berhasil dikonfirmasi!'
                        : 'Status pembayaran: ' . $actualStatus,
                    'redirect' => route('events.registered.detail', $event)
                ]);
            }

            if ($actualStatus === 'settled') {
                return redirect()->route('events.registered.detail', $event)
                    ->with('success', 'Pembayaran Tahap 2 berhasil dikonfirmasi! Anda sekarang dapat mengunggah submission tahap 2.');
            }

            return redirect()->route('events.registered.detail', $event)
                ->with('info', 'Status pembayaran Tahap 2 Anda: ' . $actualStatus);

        } catch (\Throwable $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'status' => 'pending',
                    'message' => 'Status pembayaran pending.'
                ]);
            }
            return redirect()->route('events.registered.detail', $event)
                ->with('info', 'Status pembayaran Tahap 2 pending.');
        }
    }

    public function createTeam(Event $event, Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Login dahulu untuk mendaftar.');
        }

        if (strtolower(trim($event->jenis ?? '')) !== 'lomba') {
            return redirect()->back()->with('error', 'Event ini bukan bertipe Lomba.');
        }

        if (!in_array($event->lomba_kategori, ['team', 'both'])) {
            return redirect()->back()->with('error', 'Event ini tidak membuka pendaftaran kategori Tim.');
        }

        if ($event->until_submission && \Carbon\Carbon::now()->gt($event->until_submission)) {
            return redirect()->back()->with('error', 'Pendaftaran Lomba sudah ditutup.');
        }

        if (!(bool) ($event->is_published ?? false)) {
            return redirect()->back()->with('error', 'Event belum diterbitkan.');
        }

        // Cek apakah user sudah terdaftar di event ini
        $registration = EventRegistration::where('event_id', $event->id)->where('user_id', $user->id)->first();
        if ($registration) {
            return redirect()->back()->with('info', 'Anda sudah terdaftar untuk event ini.');
        }

        $request->validate([
            'team_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'university_origin' => 'required|string|max:255',
            'institution_location' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:255',
            'info_source' => 'required|string|max:255',
            'educational_background' => 'required|string|max:255',
        ]);

        // Sync profile fields
        $profileUpdates = [];
        if ($request->full_name !== $user->name)
            $profileUpdates['name'] = $request->full_name;
        if ($request->whatsapp_number !== $user->phone)
            $profileUpdates['phone'] = $request->whatsapp_number;
        if ($request->university_origin !== $user->institution)
            $profileUpdates['institution'] = $request->university_origin;

        if (!empty($profileUpdates)) {
            $user->update($profileUpdates);
            $user->refresh();
        }

        $team = null;
        DB::transaction(function () use ($event, $user, $request, &$team) {
            // Generate unique code
            do {
                $code = Str::upper(Str::random(6));
            } while (\App\Models\Team::where('code', $code)->exists());

            $team = \App\Models\Team::create([
                'event_id' => $event->id,
                'name' => $request->team_name,
                'code' => $code,
                'leader_id' => $user->id,
                'status' => 'pending',
            ]);

            EventRegistration::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'team_id' => $team->id,
                'is_team_leader' => true,
                'status' => 'pending',
                'registration_code' => 'EVT' . $event->id . '-' . Str::upper(Str::random(6)),
                'full_name' => $request->full_name,
                'whatsapp_number' => $request->whatsapp_number,
                'university_origin' => $request->university_origin,
                'institution_location' => $request->institution_location,
                'info_source' => $request->info_source,
                'educational_background' => $request->educational_background,
            ]);
        });

        return redirect()->route('events.registered.detail', $event)->with('success', 'Tim berhasil dibuat! Bagikan kode tim Anda.');
    }

    public function joinTeam(Event $event, Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Login dahulu untuk mendaftar.');
        }

        if (strtolower(trim($event->jenis ?? '')) !== 'lomba') {
            return redirect()->back()->with('error', 'Event ini bukan bertipe Lomba.');
        }

        if (!in_array($event->lomba_kategori, ['team', 'both'])) {
            return redirect()->back()->with('error', 'Event ini tidak membuka pendaftaran kategori Tim.');
        }

        if ($event->until_submission && \Carbon\Carbon::now()->gt($event->until_submission)) {
            return redirect()->back()->with('error', 'Pendaftaran Lomba sudah ditutup.');
        }

        if (!(bool) ($event->is_published ?? false)) {
            return redirect()->back()->with('error', 'Event belum diterbitkan.');
        }

        // Cek apakah user sudah terdaftar di event ini
        $registration = EventRegistration::where('event_id', $event->id)->where('user_id', $user->id)->first();
        if ($registration) {
            return redirect()->back()->with('info', 'Anda sudah terdaftar untuk event ini.');
        }

        $request->validate([
            'team_code' => 'required|string|size:6',
            'full_name' => 'required|string|max:255',
            'university_origin' => 'required|string|max:255',
            'institution_location' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:255',
            'info_source' => 'required|string|max:255',
            'educational_background' => 'required|string|max:255',
        ]);

        // Sync profile fields
        $profileUpdates = [];
        if ($request->full_name !== $user->name)
            $profileUpdates['name'] = $request->full_name;
        if ($request->whatsapp_number !== $user->phone)
            $profileUpdates['phone'] = $request->whatsapp_number;
        if ($request->university_origin !== $user->institution)
            $profileUpdates['institution'] = $request->university_origin;

        if (!empty($profileUpdates)) {
            $user->update($profileUpdates);
            $user->refresh();
        }

        $teamCode = Str::upper($request->team_code);
        $team = \App\Models\Team::where('event_id', $event->id)
            ->where('code', $teamCode)
            ->first();

        if (!$team) {
            return redirect()->back()->with('error', 'Kode Tim tidak valid atau tidak ditemukan untuk event ini.');
        }

        if ($team->status === 'active') {
            return redirect()->back()->with('error', 'Tim ini sudah aktif/terbayar. Tidak dapat bergabung.');
        }

        // Count current members
        $currentMembersCount = EventRegistration::where('team_id', $team->id)->count();
        $maxMembers = $event->max_team_members_count;

        if ($currentMembersCount >= $maxMembers) {
            return redirect()->back()->with('error', 'Tim ini sudah penuh.');
        }

        DB::transaction(function () use ($event, $user, $team, $request) {
            EventRegistration::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'team_id' => $team->id,
                'is_team_leader' => false,
                'status' => 'pending',
                'registration_code' => 'EVT' . $event->id . '-' . Str::upper(Str::random(6)),
                'full_name' => $request->full_name,
                'whatsapp_number' => $request->whatsapp_number,
                'university_origin' => $request->university_origin,
                'institution_location' => $request->institution_location,
                'info_source' => $request->info_source,
                'educational_background' => $request->educational_background,
            ]);
        });

        return redirect()->route('events.registered.detail', $event)->with('success', 'Berhasil bergabung dengan tim!');
    }
}
