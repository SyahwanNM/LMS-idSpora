<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Transaction;
use Midtrans\CoreApi;
use App\Models\Event;
use App\Models\Course;
use App\Models\EventRegistration;
use App\Models\Payment;
use App\Models\UserNotification;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{
    protected function configureMidtrans(): void
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = (bool) config('midtrans.is_production');
        MidtransConfig::$isSanitized = (bool) config('midtrans.sanitize');
        MidtransConfig::$is3ds = (bool) config('midtrans.enable_3ds');
    }

    /**
     * Manual refresh payment status from Midtrans for course payments
     */
    public function refreshCoursePayment(Request $request, $orderId)
    {
        $this->configureMidtrans();
        $payment = Payment::where('order_id', $orderId)->first();
        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }
        try {
            $status = Transaction::status($orderId);
            $payload = json_decode(json_encode($status), true);
            $va = $payload['va_numbers'][0] ?? null;
            $payment->update([
                'transaction_id' => $payload['transaction_id'] ?? $payment->transaction_id,
                'payment_type' => $payload['payment_type'] ?? $payment->payment_type,
                'bank' => $payload['bank'] ?? ($va['bank'] ?? $payment->bank),
                'va_number' => $va['va_number'] ?? $payment->va_number,
                'status' => $payload['transaction_status'] ?? $payment->status,
                'fraud_status' => $payload['fraud_status'] ?? $payment->fraud_status,
                'pdf_url' => $payload['pdf_url'] ?? $payment->pdf_url,
                'raw_notification' => $payload,
            ]);
            return response()->json(['status' => $payment->status, 'payload' => $payload]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle course payment and redirect to Midtrans Snap payment page.
     */
    public function payCourse(Request $request, Course $course)
    {
        $user = Auth::user();
        // Prevent duplicate payment for the same course
        $alreadyEnrolled = \App\Models\Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists();
        if ($alreadyEnrolled) {
            return back()->with('error', 'Anda sudah terdaftar pada course ini.');
        }
        $name = $request->input('name', $user?->name ?? '');
        $email = $request->input('email', $user?->email ?? '');
        $kode_dial = $request->input('kode_dial', '+62');
        $whatsapp = $request->input('whatsapp', '');
        $phone = $kode_dial . $whatsapp;
        $price = $course->price ?? 0;

        // If course is free, skip Midtrans & admin validation and directly enroll
        if ((int) $price <= 0) {
            $enrollment = \App\Models\Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                ['status' => 'active', 'enrolled_at' => now(), 'enrollment_code' => 'CRS-'.strtoupper(uniqid())]
            );
            if (($enrollment->status ?? null) !== 'active') {
                $enrollment->status = 'active';
            }
            if (!$enrollment->enrolled_at) {
                $enrollment->enrolled_at = now();
            }
            if (!$enrollment->enrollment_code) {
                $enrollment->enrollment_code = 'CRS-'.strtoupper(uniqid());
            }
            $enrollment->save();

            return redirect()->route('course.learn', $course->id)
                ->with('success', 'Course gratis berhasil didaftarkan. Selamat belajar!');
        }

        $this->configureMidtrans();

        // Generate unique order ID
        $orderId = 'COURSE-' . $course->id . '-' . now()->format('YmdHis') . '-' . Str::random(5);

        // Simpan transaksi ke tabel payments
        $payment = \App\Models\Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'event_id' => null,
            'order_id' => $orderId,
            'amount' => $price,
            'status' => 'pending',
        ]);

        // Definisikan $params sebelum digunakan
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $price,
                'currency' => 'IDR',
            ],
            'item_details' => [[
                'id' => 'course-' . $course->id,
                'price' => $price,
                'quantity' => 1,
                'name' => Str::limit($course->title ?? 'Course', 50),
            ]],
            'customer_details' => [
                'first_name' => $name,
                'email' => $email,
                'phone' => $phone,
            ],
            'enabled_payments' => ['qris','bank_transfer','gopay','echannel','permata'],
            'expiry' => [
                'unit' => 'minute',
                'duration' => 15,
            ],
        ];

        try {
            $snapRes = Snap::createTransaction($params);
            $arr = json_decode(json_encode($snapRes), true);
            $snapToken = $arr['token'] ?? null;
            $redirectUrl = $arr['redirect_url'] ?? null;

            if ($snapToken || $redirectUrl) {
                $payment->update([
                    'snap_token' => $snapToken,
                    'snap_redirect_url' => $redirectUrl,
                ]);
            }
            // If this is an AJAX request (client-side Snap modal), return JSON with token
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'snapToken' => $snapToken,
                    'redirectUrl' => $redirectUrl,
                    'orderId' => $orderId,
                    'courseId' => $course->id,
                ]);
            }
            if ($redirectUrl) {
                // Prevent redirecting to placeholder domains like example.com
                $host = parse_url($redirectUrl, PHP_URL_HOST) ?: '';
                if (str_contains(strtolower($host), 'example.com')) {
                    // ignore external placeholder redirect and send user back to course detail
                    return redirect()->route('course.detail', $course->id)
                        ->with('info', 'Link pembayaran Midtrans tidak valid. Silakan coba lagi.');
                }
                return redirect()->away($redirectUrl);
            }
            return back()->with('error', 'Gagal mendapatkan link pembayaran Midtrans.');
        } catch (\Throwable $e) {
            \Log::error('[Midtrans][payCourse] error', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan pembayaran: ' . $e->getMessage());
        }
    }

    public function snapToken(Request $request, Event $event)
    {
        $this->configureMidtrans();

        $user = Auth::user();
        // Accept phone/name/email overrides from request (e.g., WhatsApp number from payment form)
        $reqPhone = trim((string) $request->input('phone', ''));
        $reqName = trim((string) $request->input('name', ''));
        $reqEmail = trim((string) $request->input('email', ''));
        // Sanitize phone to allow only + and digits
        if ($reqPhone !== '') {
            $reqPhone = preg_replace('/[^0-9\+]/', '', $reqPhone) ?? '';
        }
        // Determine price (respect discount if exists)
        $price = method_exists($event, 'hasDiscount') && $event->hasDiscount()
            ? ($event->discounted_price ?? $event->price)
            : ($event->price ?? 0);

        // If client sends an existing order_id, try to reuse it if still pending
        $resumeOrderId = trim((string)$request->query('order_id', ''));
        $existingPayment = null;
        if ($resumeOrderId !== '') {
            $existingPayment = Payment::where('order_id', $resumeOrderId)
                ->where('user_id', $user->id)
                ->where('event_id', $event->id)
                ->first();
        }

        $orderId = $existingPayment?->order_id ?? ('EVT-' . $event->id . '-' . now()->format('YmdHis') . '-' . Str::random(5));

        // Ensure valid gross amount for QRIS & others (use IDR >= 1000 for reliability in sandbox)
        $amountInt = max(1000, (int) round((float) $price));

        // If final price is 0 treat as free, don't call Midtrans
        if ((int)$price <= 0) {
            return response()->json([
                'free' => true,
                'orderId' => $orderId,
            ], 200);
        }

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amountInt,
                'currency' => 'IDR',
            ],
            'item_details' => [[
                'id' => 'event-'.$event->id,
                'price' => $amountInt,
                'quantity' => 1,
                'name' => Str::limit($event->title, 50),
            ]],
            'customer_details' => [
                'first_name' => $reqName !== '' ? $reqName : ($user?->name ?? 'Guest'),
                'email' => $reqEmail !== '' ? $reqEmail : ($user?->email ?? 'guest@example.com'),
                // Use WhatsApp number from form if provided
                'phone' => $reqPhone !== '' ? $reqPhone : null,
            ],
            // Force-enable QRIS and commonly used channels for easier testing
            'enabled_payments' => ['qris','bank_transfer','gopay','echannel','permata'],
            // Keep QR valid long enough to scan on simulator
            'expiry' => [
                'unit' => 'minute',
                'duration' => 15,
            ],
        ];
        // Remove nulls from customer_details to avoid Midtrans validation issues
        $params['customer_details'] = array_filter($params['customer_details'], function($v){ return !is_null($v) && $v !== ''; });

        if ($existingPayment) {
            // Verify status from Midtrans; if not paid/closed, reuse token if stored
            try {
                $status = Transaction::status($orderId);
                $payload = json_decode(json_encode($status), true);
                $txStatus = strtolower((string)($payload['transaction_status'] ?? ''));
                if (in_array($txStatus, ['settlement','capture','expire','cancel','deny','failure'])) {
                    // Sync DB status for the existing payment
                    try {
                        $existingPayment->update(['status' => $txStatus]);
                    } catch (\Throwable $e) { /* ignore */ }
                    // Can't resume; create a fresh order id below
                    $existingPayment = null;
                    $orderId = 'EVT-' . $event->id . '-' . now()->format('YmdHis') . '-' . Str::random(5);
                    $params['transaction_details']['order_id'] = $orderId;
                } else {
                    // Log resume intent for history
                    try {
                        ActivityLog::create([
                            'user_id' => $user->id,
                            'action' => 'payment_resume',
                            'description' => 'Melanjutkan pembayaran order '.$orderId.' untuk event '.$event->title,
                        ]);
                    } catch (\Throwable $e) { /* ignore */ }
                }
            } catch (\Throwable $e) {
                // If status lookup fails, attempt to reuse stored token if any; else proceed to create new token
            }
        }

        // If we still have an existing pending payment with a stored snap token, return it directly
        if ($existingPayment && !empty($existingPayment->snap_token)) {
            session(["midtrans_order_{$event->id}" => $orderId]);
            return response()->json([
                'snapToken' => $existingPayment->snap_token,
                'redirectUrl' => $existingPayment->snap_redirect_url,
                'clientKey' => config('midtrans.client_key'),
                'orderId' => $orderId,
                'order_id' => $orderId,
            ]);
        }

        if (!$existingPayment) {
            $existingPayment = Payment::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'order_id' => $orderId,
                'amount' => $amountInt,
                'status' => 'pending',
            ]);
            // Log initiation for history
            try {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'payment_initiated',
                    'description' => 'Memulai pembayaran order '.$orderId.' untuk event '.$event->title,
                ]);
            } catch (\Throwable $e) { /* ignore */ }
        }

        // Remember order id in session for later finalize check
        session(["midtrans_order_{$event->id}" => $orderId]);

        // For free events, we could skip payment and directly mark as registered; let frontend handle free case
        try {
            \Log::info('[Midtrans][snapToken] create', [
                'order_id' => $orderId,
                'amount' => $amountInt,
                'enabled_payments' => $params['enabled_payments'] ?? null,
            ]);
            // Create Snap transaction; capture token and redirect url for resume
            $snapRes = Snap::createTransaction($params);
            $arr = json_decode(json_encode($snapRes), true);
            $snapToken = $arr['token'] ?? null;
            $redirectUrl = $arr['redirect_url'] ?? null;

            if ($snapToken || $redirectUrl) {
                $existingPayment->update([
                    'snap_token' => $snapToken,
                    'snap_redirect_url' => $redirectUrl,
                ]);
            }

            return response()->json([
                'snapToken' => $snapToken,
                'redirectUrl' => $redirectUrl,
                'clientKey' => config('midtrans.client_key'),
                'orderId' => $orderId,
                'order_id' => $orderId,
            ]);
        } catch (\Throwable $e) {
            \Log::error('[Midtrans][snapToken] error', [ 'order_id' => $orderId, 'error' => $e->getMessage() ]);
            // Fallback to previously stored token/url to avoid initiation failure when resuming
            if ($existingPayment && !empty($existingPayment->snap_token)) {
                return response()->json([
                    'snapToken' => $existingPayment->snap_token,
                    'redirectUrl' => $existingPayment->snap_redirect_url,
                    'clientKey' => config('midtrans.client_key'),
                    'orderId' => $orderId,
                    'order_id' => $orderId,
                ]);
            }
            return response()->json([
                'error' => 'midtrans_error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Return current pending order for this user+event, or null if none
    public function pendingOrder(Request $request, Event $event)
    {
        $user = $request->user();
        if(!$user){ return response()->json(['message'=>'unauthenticated'], 401); }
        $payment = Payment::where('user_id',$user->id)
            ->where('event_id',$event->id)
            ->orderByDesc('id')
            ->first();
        if(!$payment){ return response()->json(['exists'=>false]); }
        $status = strtolower((string) $payment->status);
        // Live refresh from Midtrans to sync DB status if webhook missed
        try {
            $this->configureMidtrans();
            $statusRes = Transaction::status($payment->order_id);
            $payload = json_decode(json_encode($statusRes), true);
            $remoteStatus = strtolower((string)($payload['transaction_status'] ?? ''));
            if($remoteStatus && $remoteStatus !== $status){
                // Update DB with the latest status
                $va = $payload['va_numbers'][0] ?? null;
                $payment->update([
                    'transaction_id' => $payload['transaction_id'] ?? $payment->transaction_id,
                    'payment_type' => $payload['payment_type'] ?? $payment->payment_type,
                    'bank' => $payload['bank'] ?? ($va['bank'] ?? $payment->bank),
                    'va_number' => $va['va_number'] ?? $payment->va_number,
                    'status' => $remoteStatus,
                    'fraud_status' => $payload['fraud_status'] ?? $payment->fraud_status,
                    'pdf_url' => $payload['pdf_url'] ?? $payment->pdf_url,
                    'raw_notification' => $payload,
                ]);
                $status = $remoteStatus;
            }
        } catch (\Throwable $e) { /* ignore refresh errors */ }

        // Only expose pending-ish states to resume
        if(in_array($status, ['pending','challenge'])){
            return response()->json([
                'exists' => true,
                'order_id' => $payment->order_id,
                'status' => $payment->status,
                'snap_token' => $payment->snap_token,
                'redirect_url' => $payment->snap_redirect_url,
            ]);
        }
        return response()->json(['exists'=>false, 'status'=>$payment->status]);
    }

    public function notify(Request $request)
    {
        // Handle Midtrans HTTP Notification
        $payload = $request->all();
        \Log::info('[Midtrans][notify] incoming', [
            'order_id' => $payload['order_id'] ?? null,
            'transaction_status' => $payload['transaction_status'] ?? null,
            'status_code' => $payload['status_code'] ?? null,
            'payment_type' => $payload['payment_type'] ?? null,
        ]);
        $orderId = $payload['order_id'] ?? null;
        if(!$orderId){ return response()->json(['message' => 'invalid'], 422); }

        // Optional: verify signature key
        $signatureKey = $payload['signature_key'] ?? null;
        $serverKey = config('midtrans.server_key');
        $check = hash('sha512', ($orderId . ($payload['status_code'] ?? '') . ($payload['gross_amount'] ?? '') . $serverKey));
        if(!$signatureKey || !hash_equals($check, $signatureKey)){
            \Log::warning('[Midtrans][notify] invalid signature', [
                'order_id' => $orderId,
                'calc' => $check,
                'provided' => $signatureKey,
            ]);
            return response()->json(['message' => 'invalid signature'], 401);
        }

        $payment = Payment::where('order_id', $orderId)->first();
        if(!$payment){ return response()->json(['message' => 'order not found'], 404); }

        $va = isset($payload['va_numbers']) && is_array($payload['va_numbers']) && count($payload['va_numbers']) > 0 ? $payload['va_numbers'][0] : null;
        $payment->update([
            'transaction_id' => $payload['transaction_id'] ?? null,
            'payment_type' => $payload['payment_type'] ?? null,
            'bank' => $payload['bank'] ?? ($va['bank'] ?? null),
            'va_number' => $va['va_number'] ?? null,
            'status' => $payload['transaction_status'] ?? null,
            'fraud_status' => $payload['fraud_status'] ?? null,
            'pdf_url' => $payload['pdf_url'] ?? null,
            'raw_notification' => $payload,
        ]);
        $payment->refresh();

        // If settlement (paid), auto register the user to the course
        if($payment->course_id && in_array($payment->status, ['capture','settlement'])){
            $exists = \App\Models\Enrollment::where('user_id',$payment->user_id)->where('course_id',$payment->course_id)->exists();
            if(!$exists){
                \App\Models\Enrollment::create([
                    'user_id' => $payment->user_id,
                    'course_id' => $payment->course_id,
                    'status' => 'active',
                    'enrolled_at' => now(),
                    'enrollment_code' => 'CRS-'.strtoupper(uniqid()),
                ]);
            }
        }
        // If settlement (paid), auto register the user to the event
        if($payment->event_id && in_array($payment->status, ['capture','settlement'])){
            $exists = EventRegistration::where('user_id',$payment->user_id)->where('event_id',$payment->event_id)->exists();
            if(!$exists){
                $reg = EventRegistration::create([
                    'user_id' => $payment->user_id,
                    'event_id' => $payment->event_id,
                    'status' => 'active',
                    'registration_code' => 'EVT-'.strtoupper(uniqid()),
                    'total_price' => $payment->amount ?? 0.00,
                ]);
                // Add points for paid event registration
                try {
                    $user = \App\Models\User::find($payment->user_id);
                    $ev = Event::find($payment->event_id);
                    if($user && $ev) {
                        $pointsService = app(\App\Services\UserPointsService::class);
                        $pointsService->addEventPoints($user, $ev, $reg);
                    }
                } catch (\Throwable $e) { /* ignore */ }
                // Notification for paid registration
                try{
                    $ev = Event::find($payment->event_id);
                    if($ev){
                        UserNotification::create([
                            'user_id' => $payment->user_id,
                            'type' => 'event_registration',
                            'title' => 'Pendaftaran Dikonfirmasi',
                            'message' => 'Pendaftaran untuk "'.$ev->title.'" telah dikonfirmasi.',
                            'data' => ['url' => route('events.show', $ev)],
                            'expires_at' => now()->addDays(14),
                        ]);
                    }
                }catch(\Throwable $e){ /* ignore */ }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function finalize(Request $request, Event $event)
    {
        $this->configureMidtrans();
        $user = $request->user();
        if(!$user){
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        // Optional: verify order id matches last generated for this event
        $orderId = session("midtrans_order_{$event->id}");
        if(!$orderId){
            return response()->json(['message' => 'Order tidak ditemukan'], 422);
        }
        // Try to refresh payment status from Midtrans in case webhook didn't reach us
        $payment = Payment::where('order_id', $orderId)->first();
        $paymentStatus = null;
        try {
            $status = Transaction::status($orderId);
            $payload = json_decode(json_encode($status), true);
            $paymentStatus = $payload['transaction_status'] ?? null;
            if($payment){
                $va = $payload['va_numbers'][0] ?? null;
                $payment->update([
                    'transaction_id' => $payload['transaction_id'] ?? $payment->transaction_id,
                    'payment_type' => $payload['payment_type'] ?? $payment->payment_type,
                    'bank' => $payload['bank'] ?? ($va['bank'] ?? $payment->bank),
                    'va_number' => $va['va_number'] ?? $payment->va_number,
                    'status' => $paymentStatus ?? $payment->status,
                    'fraud_status' => $payload['fraud_status'] ?? $payment->fraud_status,
                    'pdf_url' => $payload['pdf_url'] ?? $payment->pdf_url,
                    'raw_notification' => $payload,
                ]);
            }
        } catch (\Throwable $e) {
            \Log::warning('[Midtrans][finalize] failed to refresh status', ['order_id'=>$orderId,'error'=>$e->getMessage()]);
        }

        $registered = false;
        // Register only when already paid (capture/settlement) OR if event is free
        $finalPrice = (method_exists($event,'hasDiscount') && $event->hasDiscount()) ? ($event->discounted_price ?? $event->price) : ($event->price ?? 0);
        $isFree = (int)$finalPrice <= 0;
        if($isFree || in_array($paymentStatus, ['capture','settlement'])){
            $exists = EventRegistration::where('user_id',$user->id)->where('event_id',$event->id)->exists();
            if(!$exists){
                $regData = [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'status' => 'active',
                    'registration_code' => 'EVT-'.strtoupper(uniqid()),
                    'total_price' => $payment ? ($payment->amount ?? $finalPrice) : $finalPrice,
                ];
                // if we have a payment record and a stored proof, attach it
                if($payment && !empty($payment->raw_notification) && is_array($payment->raw_notification)){
                    // nothing specific for raw_notification; keep for future
                }
                $reg = EventRegistration::create($regData);
                
                // Add points for event registration
                try {
                    $pointsService = app(\App\Services\UserPointsService::class);
                    $pointsService->addEventPoints($user, $event, $reg);
                } catch (\Throwable $e) { /* ignore */ }
                
                try{
                    UserNotification::create([
                        'user_id' => $user->id,
                        'type' => 'event_registration',
                        'title' => 'Pendaftaran Dikonfirmasi',
                        'message' => 'Pendaftaran untuk "'.$event->title.'" telah dikonfirmasi.',
                        'data' => ['url' => route('events.show', $event)],
                        'expires_at' => now()->addDays(14),
                    ]);
                }catch(\Throwable $e){ /* ignore */ }
            }
            $registered = true;
        }
        return response()->json([
            'status' => 'ok',
            'payment_status' => $paymentStatus,
            'registered' => $registered,
        ]);
    }

    /**
     * Fallback: Create QRIS via Core API and return QR string + PNG (base64) for manual scan.
     */
    public function qrisCore(Request $request, Event $event)
    {
        $this->configureMidtrans();
        $user = Auth::user();
        if(!$user){
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        // Compute effective price
        $price = (method_exists($event,'hasDiscount') && $event->hasDiscount()) ? ($event->discounted_price ?? $event->price) : ($event->price ?? 0);
        $amountInt = max(1000, (int) round((float) $price));

        $orderId = 'QRC-' . $event->id . '-' . now()->format('YmdHis') . '-' . Str::random(5);

        // Create pending payment row
        Payment::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'order_id' => $orderId,
            'amount' => $amountInt,
            'status' => 'pending',
        ]);
        session(["midtrans_order_{$event->id}" => $orderId]);

        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amountInt,
            ],
            'item_details' => [[
                'id' => 'event-'.$event->id,
                'price' => $amountInt,
                'quantity' => 1,
                'name' => Str::limit($event->title, 50),
            ]],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
        ];

        try{
            \Log::info('[Midtrans][qrisCore] charge', ['order_id'=>$orderId,'amount'=>$amountInt]);
            $res = CoreApi::charge($params);
            $arr = json_decode(json_encode($res), true);
            $qrString = $arr['qr_string'] ?? null;
            $actions = $arr['actions'] ?? [];
            $pngBase64 = null;
            if($qrString){
                try{
                    $png = QrCode::format('png')->size(280)->margin(1)->generate($qrString);
                    $pngBase64 = base64_encode($png);
                }catch(\Throwable $e){ /* QR generation optional */ }
            }
            return response()->json([
                'order_id' => $orderId,
                'qr_string' => $qrString,
                'qr_png' => $pngBase64 ? ('data:image/png;base64,'.$pngBase64) : null,
                'actions' => $actions,
            ]);
        }catch(\Throwable $e){
            \Log::error('[Midtrans][qrisCore] error', ['order_id'=>$orderId,'error'=>$e->getMessage()]);
            return response()->json([
                'error' => 'midtrans_qris_error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}