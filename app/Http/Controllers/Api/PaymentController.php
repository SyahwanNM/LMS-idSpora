<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\ManualPayment;
use App\Models\PaymentProof;
use App\Models\EventRegistration;
use App\Models\Event;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Referral;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private const REFERRAL_DISCOUNT_RATE = 0.10;
    private const MIDTRANS_METHOD = 'midtrans';

    private function notifyEventMidtransRegistrationSuccess(Event $event, ManualPayment $payment): void
    {
        try {
            $exists = UserNotification::query()
                ->where('user_id', $payment->user_id)
                ->where('type', 'event_registration_midtrans_success')
                ->where('data->order_id', $payment->order_id)
                ->exists();

            if ($exists) {
                return;
            }

            UserNotification::create([
                'user_id' => $payment->user_id,
                'type' => 'event_registration_midtrans_success',
                'title' => 'Pendaftaran Dikonfirmasi',
                'message' => 'Anda berhasil terdaftar di event "' . ($event->title ?? 'Event') . '".',
                'data' => [
                    'event_id' => $event->id,
                    'order_id' => $payment->order_id,
                    'url' => route('events.registered.detail', $event),
                ],
                'expires_at' => now()->addDays(14),
            ]);
        } catch (\Throwable $e) {
            // Do not block payment flow on notification errors.
            Log::warning('Failed to create midtrans event success notification', [
                'order_id' => $payment->order_id,
                'user_id' => $payment->user_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * List user's manual payments.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $payments = ManualPayment::with(['event', 'registration'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar pembayaran manual',
            'data' => $payments,
        ]);
    }

    /**
     * Show details of a manual payment.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $payment = ManualPayment::with(['event', 'registration', 'proofs'])
            ->where('user_id', $user->id)
            ->find($id);

        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => 'Pembayaran tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail pembayaran',
            'data' => $payment,
        ]);
    }

    /**
     * Submit manual payment (create or update proof).
     * Expects: event_id, payment_proof (file)
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'referral_code' => 'nullable|string|max:64',
        ]);

        $user = $request->user();
        $eventId = $request->input('event_id');
        $event = Event::find($eventId);

        // Find registration
        $registration = EventRegistration::where('user_id', $user->id)
            ->where('event_id', $eventId)
            ->first();

        if (!$registration) {
            return response()->json(['status' => 'error', 'message' => 'Anda belum terdaftar di event ini.'], 404);
        }

        // Determine base amount (as recorded by registration)
        $baseAmount = (float) ($registration->total_price ?? 0);
        $amount = (float) $baseAmount;
        if ($amount <= 0) {
             return response()->json(['status' => 'error', 'message' => 'Event ini gratis, tidak perlu pembayaran.'], 400);
        }

        // Referral/discount only applies for reseller-enabled events.
        $rawReferralCode = trim((string) $request->input('referral_code'));
        $referrer = (bool) ($event->is_reseller_event ?? false)
            ? $this->resolveValidReferrer($user, $rawReferralCode)
            : null;
        $referralCode = $referrer ? $rawReferralCode : null;
        $finalAmount = $this->applyReferralDiscountAmount((float) $baseAmount, $referrer !== null);

        DB::beginTransaction();
        try {
            // Find or Create ManualPayment Record
            $manualPayment = ManualPayment::where('event_registration_id', $registration->id)->first();

            if (!$manualPayment) {
                $manualPayment = ManualPayment::create([
                    'event_id' => $event->id,
                    'event_registration_id' => $registration->id,
                    'user_id' => $user->id,
                    'order_id' => 'MP-' . strtoupper(uniqid()),
                    'amount' => $finalAmount,
                    'currency' => 'IDR',
                    'method' => 'manual_transfer', // Default method
                    'status' => 'pending',
                    'referral_code' => $referralCode,
                    'metadata' => [
                        'source' => 'event',
                        'base_amount' => $baseAmount,
                        'discount_rate' => $referrer ? self::REFERRAL_DISCOUNT_RATE : 0,
                    ],
                ]);
            } else {
                 // Reset status if re-uploading
                 $manualPayment->amount = $finalAmount;
                 $manualPayment->status = 'pending';
                 $manualPayment->referral_code = $referralCode;
                 $manualPayment->metadata = array_merge((array) ($manualPayment->metadata ?? []), [
                     'source' => 'event',
                     'base_amount' => $baseAmount,
                     'discount_rate' => $referrer ? self::REFERRAL_DISCOUNT_RATE : 0,
                 ]);
                 $manualPayment->save();
            }

            // Handle File Upload
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $path = $file->store('payments', 'public');

                // Create Proof Record
                PaymentProof::create([
                    'manual_payment_id' => $manualPayment->id,
                    'event_registration_id' => $registration->id,
                    'file_path' => $path,
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => $user->id,
                ]);

                // Update legacy field
                $registration->update([
                    'status' => 'pending', // Ensure status is pending waiting for admin
                    'total_price' => $finalAmount,
                    'payment_proof' => $path
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Bukti pembayaran berhasil diupload. Mohon tunggu verifikasi admin.',
                'data' => [
                    'manual_payment' => $manualPayment->load('proofs'),
                    'amount_breakdown' => [
                        'base_amount' => (float) $baseAmount,
                        'discount_rate' => $referrer ? self::REFERRAL_DISCOUNT_RATE : 0,
                        'final_amount' => (float) $finalAmount,
                        'referral_applied' => $referrer !== null,
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update proof for an existing payment (using POST because of file upload).
     */
    public function update(Request $request, $id)
    {
        // Effectively same as store but targeting specific payment ID
        // For simplicity, let's allow users to just use store endpoint generally, 
        // OR implements strict update here. 
        
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'referral_code' => 'nullable|string|max:64',
        ]);

        $user = $request->user();
        $manualPayment = ManualPayment::where('user_id', $user->id)->find($id);

        if (!$manualPayment) {
            return response()->json(['status' => 'error', 'message' => 'Pembayaran tidak ditemukan'], 404);
        }

        $event = $manualPayment->event_id ? Event::find($manualPayment->event_id) : null;
        $registration = $manualPayment->event_registration_id ? EventRegistration::find($manualPayment->event_registration_id) : null;
        $baseAmount = (float) ($registration?->total_price ?? $manualPayment->amount ?? 0);

        $rawReferralCode = trim((string) $request->input('referral_code'));
        $referrer = ($event && (bool) ($event->is_reseller_event ?? false))
            ? $this->resolveValidReferrer($user, $rawReferralCode)
            : null;
        $referralCode = $referrer ? $rawReferralCode : null;
        $finalAmount = $this->applyReferralDiscountAmount((float) $baseAmount, $referrer !== null);

        DB::beginTransaction();
        try {
            $file = $request->file('payment_proof');
            $path = $file->store('payments', 'public');

            PaymentProof::create([
                'manual_payment_id' => $manualPayment->id,
                'event_registration_id' => $manualPayment->event_registration_id,
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $user->id,
            ]);

            $manualPayment->update(['status' => 'pending']);
            $manualPayment->amount = $finalAmount;
            $manualPayment->referral_code = $referralCode;
            $manualPayment->metadata = array_merge((array) ($manualPayment->metadata ?? []), [
                'source' => 'event',
                'base_amount' => $baseAmount,
                'discount_rate' => $referrer ? self::REFERRAL_DISCOUNT_RATE : 0,
            ]);
            $manualPayment->save();
            
            // Update legacy fields
            if ($registration) {
                $registration->update([
                    'status' => 'pending',
                    'total_price' => $finalAmount,
                    'payment_proof' => $path,
                ]);
            }

            DB::commit();

             return response()->json([
                'status' => 'success',
                'message' => 'Bukti pembayaran berhasil diperbarui.',
                'data' => [
                    'manual_payment' => $manualPayment->load('proofs'),
                    'amount_breakdown' => [
                        'base_amount' => (float) $baseAmount,
                        'discount_rate' => $referrer ? self::REFERRAL_DISCOUNT_RATE : 0,
                        'final_amount' => (float) $finalAmount,
                        'referral_applied' => $referrer !== null,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal update pembayaran: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Cancel manual payment.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $manualPayment = ManualPayment::where('user_id', $user->id)->find($id);

        if (!$manualPayment) {
            return response()->json(['status' => 'error', 'message' => 'Pembayaran tidak ditemukan'], 404);
        }

        if ($manualPayment->status == 'paid' || $manualPayment->status == 'verified') {
             return response()->json(['status' => 'error', 'message' => 'Pembayaran yang sudah diverifikasi tidak dapat dibatalkan.'], 400);
        }

        DB::beginTransaction();
        try {
            $manualPayment->update(['status' => 'cancelled']); // or 'canceled' check enum consistency
            
            $registration = EventRegistration::find($manualPayment->event_registration_id);
            if($registration){
                $registration->update(['status' => 'canceled']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran dibatalkan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
             return response()->json(['status' => 'error', 'message' => 'Gagal membatalkan: ' . $e->getMessage()], 500);
        }
    }

    private function resolveValidReferrer(?User $buyer, ?string $referralCode): ?User
    {
        $code = trim((string) $referralCode);
        if ($code === '') {
            return null;
        }

        $referrer = User::query()->where('referral_code', $code)->first();
        if (!$referrer) {
            return null;
        }

        if ($buyer && (int) $referrer->id === (int) $buyer->id) {
            return null;
        }

        return $referrer;
    }

    private function applyReferralDiscountAmount(float $baseAmount, bool $hasValidReferral): float
    {
        $base = max(0, (float) $baseAmount);
        if (!$hasValidReferral) {
            return $base;
        }

        $discounted = $base * (1 - self::REFERRAL_DISCOUNT_RATE);
        return max(0, round($discounted, 2));
    }

    private function configureMidtrans(): void
    {
        $serverKey = (string) config('midtrans.server_key');
        if (trim($serverKey) === '') {
            throw new \RuntimeException('Midtrans server key belum dikonfigurasi. Set MIDTRANS_SERVER_KEY.');
        }

        \Midtrans\Config::$serverKey = $serverKey;
        \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = (bool) config('midtrans.sanitize', true);
        \Midtrans\Config::$is3ds = (bool) config('midtrans.3ds', true);
    }

    private function getMidtransClientKey(): string
    {
        return (string) config('midtrans.client_key');
    }

    private function mapMidtransToInternalStatus(?string $transactionStatus, ?string $fraudStatus = null): string
    {
        $ts = strtolower((string) $transactionStatus);
        $fs = strtolower((string) $fraudStatus);

        // Capture can be challenged by fraud status.
        if ($ts === 'capture') {
            return $fs === 'challenge' ? 'pending' : 'settled';
        }

        if ($ts === 'settlement') {
            return 'settled';
        }

        if ($ts === 'pending') {
            return 'pending';
        }

        if ($ts === 'expire') {
            return 'expired';
        }

        // deny/cancel/failure/refund/chargeback -> treat as rejected.
        return 'rejected';
    }

    private function buildMidtransSnapParams(string $orderId, int $grossAmount, array $itemDetails, array $customerDetails): array
    {
        $configFinishUrl = (string) (config('midtrans.finish_url') ?? '');
        $appUrl = (string) (config('app.url') ?? '');

        // Only use configured finish_url if it belongs to the same domain as the app
        if ($configFinishUrl !== '' && $appUrl !== '' && str_contains($configFinishUrl, parse_url($appUrl, PHP_URL_HOST) ?? '')) {
            $finishUrl = $configFinishUrl;
        } else {
            $finishUrl = route('payment.finish');
        }

        return [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
            'callbacks' => [
                'finish' => $finishUrl,
            ],
        ];
    }

    private function getSnapTokenFromPayment(?ManualPayment $payment): ?string
    {
        if (!$payment) {
            return null;
        }

        $token = data_get($payment->metadata, 'snap_token');
        if (!is_string($token)) {
            return null;
        }

        $token = trim($token);
        return $token !== '' ? $token : null;
    }

    private function storeSnapTokenToPayment(ManualPayment $payment, string $snapToken): void
    {
        $payment->metadata = array_merge((array) ($payment->metadata ?? []), [
            'snap_token' => $snapToken,
            'snap_token_created_at' => now()->toIso8601String(),
        ]);
        $payment->save();
    }

    private function rejectPaymentAsReplaced(ManualPayment $payment): void
    {
        if ($payment->status !== 'pending') {
            return;
        }

        $payment->status = 'rejected';
        $payment->rejection_reason = 'Diganti karena membuat transaksi Midtrans baru.';
        $payment->metadata = array_merge((array) ($payment->metadata ?? []), [
            'replaced_at' => now()->toIso8601String(),
        ]);
        $payment->save();
    }

    private function processEventReferralCommission(Event $event, ManualPayment $payment): void
    {
        if (!(bool) ($event->is_reseller_event ?? false)) {
            return;
        }
        if (empty($payment->referral_code)) {
            return;
        }

        $referrer = User::query()->where('referral_code', $payment->referral_code)->first();
        if (!$referrer || (int) $referrer->id === (int) $payment->user_id) {
            return;
        }

        $commissionAmount = ((float) $payment->amount) * 0.10;
        if ($commissionAmount <= 0) {
            return;
        }

        $existingReferral = Referral::query()
            ->where('user_id', $referrer->id)
            ->where('referred_user_id', $payment->user_id)
            ->where('description', 'Komisi Event: ' . $event->title)
            ->first();

        if ($existingReferral) {
            return;
        }

        Referral::create([
            'user_id' => $referrer->id,
            'referred_user_id' => $payment->user_id,
            'amount' => $commissionAmount,
            'status' => 'paid',
            'description' => 'Komisi Event: ' . $event->title,
        ]);

        $referrer->increment('wallet_balance', $commissionAmount);
    }

    private function processCourseReferralCommission(Course $course, ManualPayment $payment): void
    {
        if (!(bool) ($course->is_reseller_course ?? false)) {
            return;
        }
        if (empty($payment->referral_code)) {
            return;
        }

        $referrer = User::query()->where('referral_code', $payment->referral_code)->first();
        if (!$referrer || (int) $referrer->id === (int) $payment->user_id) {
            return;
        }

        $commissionAmount = ((float) $payment->amount) * 0.10;
        if ($commissionAmount <= 0) {
            return;
        }

        $existingReferral = Referral::query()
            ->where('user_id', $referrer->id)
            ->where('referred_user_id', $payment->user_id)
            ->where('description', 'Komisi Course: ' . $course->name)
            ->first();

        if ($existingReferral) {
            return;
        }

        Referral::create([
            'user_id' => $referrer->id,
            'referred_user_id' => $payment->user_id,
            'amount' => $commissionAmount,
            'status' => 'paid',
            'description' => 'Komisi Course: ' . $course->name,
        ]);

        $referrer->increment('wallet_balance', $commissionAmount);
    }

    /**
     * Midtrans Snap token for paid Event (auth required).
     */
    public function snapToken(Request $request, Event $event): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $this->configureMidtrans();

        $forceNew = (bool) $request->boolean('force_new');

        $hasDiscount = method_exists($event, 'hasDiscount') ? (bool) $event->hasDiscount() : false;
        $baseAmount = (float) ($hasDiscount ? ($event->discounted_price ?? $event->price) : ($event->price ?? 0));
        if ($baseAmount <= 0) {
            return response()->json(['message' => 'Event gratis, tidak perlu Midtrans.'], 400);
        }

        $rawReferralCode = trim((string) $request->query('referral_code', $request->input('referral_code')));
        $referrer = (bool) ($event->is_reseller_event ?? false)
            ? $this->resolveValidReferrer($user, $rawReferralCode)
            : null;
        $referralCode = $referrer ? $rawReferralCode : null;
        $finalAmount = $this->applyReferralDiscountAmount($baseAmount, $referrer !== null);

        $dial = trim((string) $request->query('dial_code', $request->input('dial_code')));
        $wa = trim((string) $request->query('whatsapp', $request->input('whatsapp')));
        $phone = trim($dial . $wa);
        if ($phone === '') {
            $phone = (string) ($user->phone ?? '');
        }

        DB::beginTransaction();
        try {
            // Ensure a pending registration exists
            $registration = EventRegistration::query()
                ->where('user_id', $user->id)
                ->where('event_id', $event->id)
                ->first();

            if ($registration && $registration->status === 'active') {
                DB::rollBack();
                return response()->json(['message' => 'Anda sudah terdaftar.'], 409);
            }

            if (!$registration) {
                $registration = EventRegistration::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'status' => 'pending',
                    'registration_code' => 'EVT-' . strtoupper(uniqid()),
                    'total_price' => $finalAmount,
                ]);
            } else {
                $registration->status = 'pending';
                $registration->total_price = $finalAmount;
                $registration->save();
            }

            // Reuse existing pending midtrans order if any
            $payment = ManualPayment::query()
                ->where('event_registration_id', $registration->id)
                ->where('user_id', $user->id)
                ->where('method', self::MIDTRANS_METHOD)
                ->where('status', 'pending')
                ->latest('id')
                ->first();

            if ($payment && !$forceNew) {
                $existingToken = $this->getSnapTokenFromPayment($payment);
                if ($existingToken) {
                    $registration->status = 'pending';
                    $registration->total_price = (float) $payment->amount;
                    $registration->save();

                    DB::commit();
                    return response()->json([
                        'snap_token' => $existingToken,
                        'order_id' => $payment->order_id,
                        'amount' => (int) round((float) $payment->amount),
                        'client_key' => $this->getMidtransClientKey(),
                        'is_production' => (bool) config('midtrans.is_production', false),
                        'is_pending' => true,
                        'is_continue' => true,
                    ]);
                }
            }

            if ($payment && $forceNew) {
                $this->rejectPaymentAsReplaced($payment);
                $payment = null;
            }

            if (!$payment) {
                $payment = new ManualPayment();
                $payment->order_id = 'MT-EVT-' . strtoupper(uniqid());
            }

            $payment->fill([
                'event_id' => $event->id,
                'event_registration_id' => $registration->id,
                'user_id' => $user->id,
                'amount' => $finalAmount,
                'currency' => 'IDR',
                'method' => self::MIDTRANS_METHOD,
                'status' => 'pending',
                'whatsapp_number' => $phone ?: null,
                'referral_code' => $referralCode,
                'rejection_reason' => null,
                'metadata' => array_merge((array) ($payment->metadata ?? []), [
                    'source' => 'event',
                    'base_amount' => $baseAmount,
                    'discount_rate' => $referrer ? self::REFERRAL_DISCOUNT_RATE : 0,
                    'event_id' => $event->id,
                    'event_title' => $event->title,
                ]),
            ]);
            $payment->save();

            $grossAmount = (int) round($finalAmount);
            $snapParams = $this->buildMidtransSnapParams(
                (string) $payment->order_id,
                $grossAmount,
                [[
                    'id' => 'event-' . $event->id,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => (string) ($event->title ?? 'Event'),
                ]],
                [
                    'first_name' => (string) ($user->name ?? 'User'),
                    'email' => (string) ($user->email ?? ''),
                    'phone' => $phone,
                ]
            );

            Log::info('Midtrans snapParams(event)', ['params' => $snapParams]);

            $snapToken = \Midtrans\Snap::getSnapToken($snapParams);
            
            Log::info('Midtrans snapToken(event) created', [
                'order_id' => $payment->order_id,
                'snap_token' => $snapToken
            ]);

            // Persist token so user can continue when still pending
            $this->storeSnapTokenToPayment($payment, $snapToken);

            $registration->payment_url = null;
            $registration->save();

            DB::commit();

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $payment->order_id,
                'amount' => $grossAmount,
                'client_key' => $this->getMidtransClientKey(),
                'is_production' => (bool) config('midtrans.is_production', false),
            ]);
        } catch (\Throwable $e) {
            // Retry once with a new order_id when Midtrans rejects reused order_id
            $msg = strtolower((string) $e->getMessage());
            $shouldRetry = str_contains($msg, 'order id') || str_contains($msg, 'order_id') || str_contains($msg, 'already') || str_contains($msg, 'duplicate');

            DB::rollBack();

            if ($shouldRetry) {
                try {
                    DB::beginTransaction();

                    $registration = EventRegistration::query()
                        ->where('user_id', $user->id)
                        ->where('event_id', $event->id)
                        ->first();

                    if (!$registration || $registration->status === 'active') {
                        DB::rollBack();
                        return response()->json(['message' => 'Anda sudah terdaftar.'], 409);
                    }

                    $existingPending = ManualPayment::query()
                        ->where('event_registration_id', $registration->id)
                        ->where('user_id', $user->id)
                        ->where('method', self::MIDTRANS_METHOD)
                        ->where('status', 'pending')
                        ->latest('id')
                        ->first();

                    if ($existingPending) {
                        $this->rejectPaymentAsReplaced($existingPending);
                    }

                    $newPayment = new ManualPayment();
                    $newPayment->order_id = 'MT-EVT-' . strtoupper(uniqid());
                    $newPayment->fill([
                        'event_id' => $event->id,
                        'event_registration_id' => $registration->id,
                        'user_id' => $user->id,
                        'amount' => $finalAmount,
                        'currency' => 'IDR',
                        'method' => self::MIDTRANS_METHOD,
                        'status' => 'pending',
                        'whatsapp_number' => $phone ?: null,
                        'referral_code' => $referralCode,
                        'rejection_reason' => null,
                        'metadata' => [
                            'source' => 'event',
                            'retry_reason' => 'order_id_conflict',
                            'event_id' => $event->id,
                            'event_title' => $event->title,
                        ],
                    ]);
                    $newPayment->save();

                    $grossAmount = (int) round($finalAmount);
                    $snapParams = $this->buildMidtransSnapParams(
                        (string) $newPayment->order_id,
                        $grossAmount,
                        [[
                            'id' => 'event-' . $event->id,
                            'price' => $grossAmount,
                            'quantity' => 1,
                            'name' => (string) ($event->title ?? 'Event'),
                        ]],
                        [
                            'first_name' => (string) ($user->name ?? 'User'),
                            'email' => (string) ($user->email ?? ''),
                            'phone' => $phone,
                        ]
                    );

                    $snapToken = \Midtrans\Snap::getSnapToken($snapParams);
                    $this->storeSnapTokenToPayment($newPayment, $snapToken);

                    DB::commit();
                    return response()->json([
                        'snap_token' => $snapToken,
                        'order_id' => $newPayment->order_id,
                        'amount' => $grossAmount,
                        'client_key' => $this->getMidtransClientKey(),
                        'is_production' => (bool) config('midtrans.is_production', false),
                    ]);
                } catch (\Throwable $retryException) {
                    DB::rollBack();
                    Log::error('Midtrans snapToken(event) retry failed', ['error' => $retryException->getMessage()]);
                }
            }

            Log::error('Midtrans snapToken(event) failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
                'event_id' => $event->id
            ]);
            return response()->json(['message' => 'Gagal membuat pembayaran Midtrans: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Midtrans Snap token for paid Course (auth required).
     */
    public function courseSnapToken(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $this->configureMidtrans();

        $forceNew = (bool) $request->boolean('force_new');

        $hasDiscount = method_exists($course, 'hasDiscount') ? (bool) $course->hasDiscount() : false;
        $baseAmount = (float) ($hasDiscount ? ($course->discounted_price ?? $course->price) : ($course->price ?? 0));
        if ($baseAmount <= 0) {
            return response()->json(['message' => 'Course gratis, tidak perlu Midtrans.'], 400);
        }

        $rawReferralCode = trim((string) $request->query('referral_code', $request->input('referral_code')));
        if (
            $rawReferralCode !== ''
            && (bool) ($course->is_reseller_course ?? false)
            && $user->referral_code
            && strcasecmp($rawReferralCode, (string) $user->referral_code) === 0
        ) {
            return response()->json([
                'message' => 'Kode referral tidak boleh menggunakan kode milik sendiri.',
            ], 422);
        }

        $referrer = (bool) ($course->is_reseller_course ?? false)
            ? $this->resolveValidReferrer($user, $rawReferralCode)
            : null;
        if ($rawReferralCode !== '' && (bool) ($course->is_reseller_course ?? false) && !$referrer) {
            return response()->json([
                'message' => 'Kode referral tidak valid.',
            ], 422);
        }
        $referralCode = $referrer ? $rawReferralCode : null;
        $finalAmount = $this->applyReferralDiscountAmount($baseAmount, $referrer !== null);

        $dial = trim((string) $request->query('dial_code', $request->input('dial_code')));
        $wa = trim((string) $request->query('whatsapp', $request->input('whatsapp')));
        $phone = trim($dial . $wa);

        DB::beginTransaction();
        try {
            $enrollment = Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                ['status' => 'pending']
            );
            if ($enrollment->status !== 'active') {
                $enrollment->status = 'pending';
                $enrollment->save();
            }

            // Reuse existing pending midtrans order if any
            $payment = ManualPayment::query()
                ->where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->where('method', self::MIDTRANS_METHOD)
                ->where('status', 'pending')
                ->latest('id')
                ->first();

            if ($payment && !$forceNew) {
                $existingToken = $this->getSnapTokenFromPayment($payment);
                if ($existingToken) {
                    DB::commit();
                    return response()->json([
                        'snap_token' => $existingToken,
                        'order_id' => $payment->order_id,
                        'amount' => (int) round((float) $payment->amount),
                        'client_key' => $this->getMidtransClientKey(),
                        'is_production' => (bool) config('midtrans.is_production', false),
                        'is_pending' => true,
                        'is_continue' => true,
                    ]);
                }
            }

            if ($payment && $forceNew) {
                $this->rejectPaymentAsReplaced($payment);
                $payment = null;
            }

            if (!$payment) {
                $payment = new ManualPayment();
                $payment->order_id = 'MT-CRS-' . strtoupper(uniqid());
            }

            $payment->fill([
                'course_id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'user_id' => $user->id,
                'amount' => $finalAmount,
                'currency' => 'IDR',
                'method' => self::MIDTRANS_METHOD,
                'status' => 'pending',
                'whatsapp_number' => $phone ?: null,
                'referral_code' => $referralCode,
                'rejection_reason' => null,
                'metadata' => array_merge((array) ($payment->metadata ?? []), [
                    'source' => 'course',
                    'base_amount' => $baseAmount,
                    'discount_rate' => $referrer ? self::REFERRAL_DISCOUNT_RATE : 0,
                    'course_id' => $course->id,
                    'course_name' => $course->name,
                ]),
            ]);
            $payment->save();

            $grossAmount = (int) round($finalAmount);
            $snapParams = $this->buildMidtransSnapParams(
                (string) $payment->order_id,
                $grossAmount,
                [[
                    'id' => 'course-' . $course->id,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => (string) ($course->name ?? 'Course'),
                ]],
                [
                    'first_name' => (string) ($user->name ?? 'User'),
                    'email' => (string) ($user->email ?? ''),
                    'phone' => $phone,
                ]
            );

            Log::info('Midtrans snapParams(course)', ['params' => $snapParams]);
            
            $snapToken = \Midtrans\Snap::getSnapToken($snapParams);

            Log::info('Midtrans courseSnapToken created', [
                'order_id' => $payment->order_id,
                'snap_token' => $snapToken
            ]);
            
            $this->storeSnapTokenToPayment($payment, $snapToken);
            DB::commit();

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $payment->order_id,
                'amount' => $grossAmount,
                'client_key' => $this->getMidtransClientKey(),
                'is_production' => (bool) config('midtrans.is_production', false),
            ]);
        } catch (\Throwable $e) {
            $msg = strtolower((string) $e->getMessage());
            $shouldRetry = str_contains($msg, 'order id') || str_contains($msg, 'order_id') || str_contains($msg, 'already') || str_contains($msg, 'duplicate');
            DB::rollBack();

            if ($shouldRetry) {
                try {
                    DB::beginTransaction();

                    $enrollment = Enrollment::firstOrCreate(
                        ['user_id' => $user->id, 'course_id' => $course->id],
                        ['status' => 'pending']
                    );
                    if ($enrollment->status !== 'active') {
                        $enrollment->status = 'pending';
                        $enrollment->save();
                    }

                    $existingPending = ManualPayment::query()
                        ->where('course_id', $course->id)
                        ->where('user_id', $user->id)
                        ->where('method', self::MIDTRANS_METHOD)
                        ->where('status', 'pending')
                        ->latest('id')
                        ->first();

                    if ($existingPending) {
                        $this->rejectPaymentAsReplaced($existingPending);
                    }

                    $newPayment = new ManualPayment();
                    $newPayment->order_id = 'MT-CRS-' . strtoupper(uniqid());
                    $newPayment->fill([
                        'course_id' => $course->id,
                        'enrollment_id' => $enrollment->id,
                        'user_id' => $user->id,
                        'amount' => $finalAmount,
                        'currency' => 'IDR',
                        'method' => self::MIDTRANS_METHOD,
                        'status' => 'pending',
                        'whatsapp_number' => $phone ?: null,
                        'referral_code' => $referralCode,
                        'rejection_reason' => null,
                        'metadata' => [
                            'source' => 'course',
                            'retry_reason' => 'order_id_conflict',
                            'course_id' => $course->id,
                            'course_name' => $course->name,
                        ],
                    ]);
                    $newPayment->save();

                    $grossAmount = (int) round($finalAmount);
                    $snapParams = $this->buildMidtransSnapParams(
                        (string) $newPayment->order_id,
                        $grossAmount,
                        [[
                            'id' => 'course-' . $course->id,
                            'price' => $grossAmount,
                            'quantity' => 1,
                            'name' => (string) ($course->name ?? 'Course'),
                        ]],
                        [
                            'first_name' => (string) ($user->name ?? 'User'),
                            'email' => (string) ($user->email ?? ''),
                            'phone' => $phone,
                        ]
                    );

                    $snapToken = \Midtrans\Snap::getSnapToken($snapParams);
                    $this->storeSnapTokenToPayment($newPayment, $snapToken);

                    DB::commit();
                    return response()->json([
                        'snap_token' => $snapToken,
                        'order_id' => $newPayment->order_id,
                        'amount' => $grossAmount,
                        'client_key' => $this->getMidtransClientKey(),
                        'is_production' => (bool) config('midtrans.is_production', false),
                    ]);
                } catch (\Throwable $retryException) {
                    DB::rollBack();
                    Log::error('Midtrans courseSnapToken retry failed', ['error' => $retryException->getMessage()]);
                }
            }

            Log::error('Midtrans courseSnapToken failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
                'course_id' => $course->id
            ]);
            return response()->json(['message' => 'Gagal membuat pembayaran Midtrans: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Webhook notification from Midtrans (no auth).
     */
    public function notify(Request $request): JsonResponse
    {
        $serverKey = (string) config('midtrans.server_key');
        if (trim($serverKey) === '') {
            return response()->json(['message' => 'Midtrans not configured'], 500);
        }

        $orderId = (string) $request->input('order_id');
        $statusCode = (string) $request->input('status_code');
        $grossAmount = (string) $request->input('gross_amount');
        $signatureKey = (string) $request->input('signature_key');

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if (!hash_equals($expected, $signatureKey)) {
            Log::warning('Midtrans notify invalid signature', ['order_id' => $orderId]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $payment = ManualPayment::query()->where('order_id', $orderId)->first();
        if (!$payment) {
            // Do not throw 404 to avoid repeated retries; just accept.
            Log::warning('Midtrans notify order not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'OK']);
        }

        $transactionStatus = (string) $request->input('transaction_status');
        $fraudStatus = (string) $request->input('fraud_status');
        $internalStatus = $this->mapMidtransToInternalStatus($transactionStatus, $fraudStatus);

        DB::beginTransaction();
        try {
            $wasSettled = $payment->status === 'settled';

            $payment->status = $internalStatus;
            $payment->metadata = array_merge((array) ($payment->metadata ?? []), [
                'midtrans' => $request->all(),
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'notified_at' => now()->toIso8601String(),
            ]);
            $payment->save();

            if (!$wasSettled && $internalStatus === 'settled') {
                // Activate related entities
                if ($payment->event_registration_id) {
                    $registration = EventRegistration::find($payment->event_registration_id);
                    if ($registration && $registration->status !== 'active') {
                        $registration->status = 'active';
                        $registration->payment_verified_at = now();
                        $registration->payment_verified_by = null;
                        $registration->save();
                    }

                    $event = $payment->event_id ? Event::find($payment->event_id) : null;
                    if ($event) {
                        $this->processEventReferralCommission($event, $payment);
                        $this->notifyEventMidtransRegistrationSuccess($event, $payment);
                    }
                }

                if ($payment->enrollment_id) {
                    $enrollment = Enrollment::find($payment->enrollment_id);
                    if ($enrollment && $enrollment->status !== 'active') {
                        $enrollment->status = 'active';
                        $enrollment->save();
                    }
                    $course = $payment->course_id ? Course::find($payment->course_id) : null;
                    if ($course) {
                        $this->processCourseReferralCommission($course, $payment);
                    }
                }
            }

            // Persist terminal non-success statuses to the related entities
            // so they don't remain pending forever when Midtrans expires or is rejected.
            if (in_array($internalStatus, ['expired', 'rejected'], true)) {
                if ($payment->event_registration_id) {
                    $registration = EventRegistration::find($payment->event_registration_id);
                    if ($registration && $registration->status !== 'active' && $registration->status !== 'canceled') {
                        $registration->status = $internalStatus;
                        $registration->save();
                    }
                }

                if ($payment->enrollment_id) {
                    $enrollment = Enrollment::find($payment->enrollment_id);
                    if ($enrollment && $enrollment->status !== 'active') {
                        $enrollment->status = $internalStatus;
                        $enrollment->save();
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'OK']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Midtrans notify failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * User redirect landing page after Midtrans Snap.
     * Midtrans appends query params like: order_id, transaction_status, status_code.
     *
     * Goal:
     * - If expired/canceled/failed, redirect user back to payment page with force_new=1.
     * - If pending, redirect to payment page (continue).
     * - If settled, redirect to event/course detail.
     */
    public function finishRedirect(Request $request)
    {
        $orderId = trim((string) $request->query('order_id'));
        if ($orderId === '') {
            return redirect()->route('dashboard')->with('success', 'Pembayaran sedang diproses.');
        }

        $user = $request->user();
        if (!$user) {
            return redirect()->route('login')->with('warning', 'Silakan login untuk melihat status pembayaran.');
        }

        $payment = ManualPayment::query()
            ->where('order_id', $orderId)
            ->where('user_id', $user->id)
            ->first();

        if (!$payment) {
            return redirect()->route('dashboard')->with('warning', 'Transaksi tidak ditemukan.');
        }

        // Prefer server-to-server status check; fallback to query param if API call fails.
        $transactionStatus = null;
        $fraudStatus = null;
        $midtransStatus = null;
        try {
            $this->configureMidtrans();
            $midtransStatus = (array) \Midtrans\Transaction::status($orderId);
            $transactionStatus = isset($midtransStatus['transaction_status']) ? (string) $midtransStatus['transaction_status'] : null;
            $fraudStatus = isset($midtransStatus['fraud_status']) ? (string) $midtransStatus['fraud_status'] : null;
        } catch (\Throwable $e) {
            $transactionStatus = (string) $request->query('transaction_status');
            $fraudStatus = (string) $request->query('fraud_status');
        }

        $internalStatus = $this->mapMidtransToInternalStatus($transactionStatus, $fraudStatus);

        DB::beginTransaction();
        try {
            $wasSettled = $payment->status === 'settled';

            // Update payment status + metadata.
            $payment->status = $internalStatus;
            $payment->metadata = array_merge((array) ($payment->metadata ?? []), [
                'finish_query' => $request->query(),
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'midtrans_status' => $midtransStatus,
                'finish_redirected_at' => now()->toIso8601String(),
            ]);
            $payment->save();

            if (!$wasSettled && $internalStatus === 'settled') {
                // Activate related entities (mirrors notify/finalize behavior).
                if ($payment->event_registration_id) {
                    $registration = EventRegistration::find($payment->event_registration_id);
                    if ($registration && $registration->status !== 'active') {
                        $registration->status = 'active';
                        $registration->payment_verified_at = now();
                        $registration->payment_verified_by = null;
                        $registration->save();
                    }

                    $event = $payment->event_id ? Event::find($payment->event_id) : null;
                    if ($event) {
                        $this->processEventReferralCommission($event, $payment);
                        $this->notifyEventMidtransRegistrationSuccess($event, $payment);
                    }
                }

                if ($payment->enrollment_id) {
                    $enrollment = Enrollment::find($payment->enrollment_id);
                    if ($enrollment && $enrollment->status !== 'active') {
                        $enrollment->status = 'active';
                        $enrollment->save();
                    }
                    $course = $payment->course_id ? Course::find($payment->course_id) : null;
                    if ($course) {
                        $this->processCourseReferralCommission($course, $payment);
                    }
                }
            }

            // Persist terminal non-success statuses to the related entities
            // so they don't remain pending forever when Midtrans expires or is rejected.
            if (in_array($internalStatus, ['expired', 'rejected'], true)) {
                if ($payment->event_registration_id) {
                    $registration = EventRegistration::find($payment->event_registration_id);
                    if ($registration && $registration->status !== 'active' && $registration->status !== 'canceled') {
                        $registration->status = $internalStatus;
                        $registration->save();
                    }
                }

                if ($payment->enrollment_id) {
                    $enrollment = Enrollment::find($payment->enrollment_id);
                    if ($enrollment && $enrollment->status !== 'active') {
                        $enrollment->status = $internalStatus;
                        $enrollment->save();
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Midtrans finishRedirect update failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);
        }

        // Redirect user based on entity type.
        if ($payment->event_id) {
            $event = Event::find($payment->event_id);
            if ($event) {
                if ($internalStatus === 'settled') {
                    return redirect()->route('events.registered.detail', $event)->with('success', 'Pembayaran berhasil.');
                }
                if ($internalStatus === 'pending') {
                    return redirect()->route('payment', $event)->with('info', 'Pembayaran masih pending. Silakan selesaikan di Midtrans.');
                }
                return redirect()->route('payment', ['event' => $event->id, 'force_new' => 1])
                    ->with('warning', 'Transaksi sudah kadaluarsa / gagal. Silakan lakukan pembayaran ulang.');
            }
        }

        if ($payment->course_id) {
            $course = Course::find($payment->course_id);
            if ($course) {
                if ($internalStatus === 'settled') {
                    return redirect()->route('courses.show', $course)->with('success', 'Pembayaran berhasil.');
                }
                if ($internalStatus === 'pending') {
                    return redirect()->route('course.payment', $course)->with('info', 'Pembayaran masih pending. Silakan selesaikan di Midtrans.');
                }
                return redirect()->route('course.payment', ['course' => $course->id, 'force_new' => 1])
                    ->with('warning', 'Transaksi sudah kadaluarsa / gagal. Silakan lakukan pembayaran ulang.');
            }
        }

        return redirect()->route('dashboard')->with('info', 'Status pembayaran sudah diperbarui.');
    }

    /**
     * Client-side: finalize event payment after Snap.
     */
    public function finalize(Request $request, Event $event): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'order_id' => ['required', 'string', 'max:100'],
        ]);
        $orderId = (string) $validated['order_id'];

        $payment = ManualPayment::query()
            ->where('order_id', $orderId)
            ->where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'Order tidak ditemukan.'], 404);
        }

        try {
            $this->configureMidtrans();
            $status = (array) \Midtrans\Transaction::status($orderId);
            $internalStatus = $this->mapMidtransToInternalStatus(
                $status['transaction_status'] ?? null,
                $status['fraud_status'] ?? null
            );

            DB::beginTransaction();
            $wasSettled = $payment->status === 'settled';
            $payment->status = $internalStatus;
            $payment->metadata = array_merge((array) ($payment->metadata ?? []), [
                'midtrans_status' => $status,
                'refreshed_at' => now()->toIso8601String(),
            ]);
            $payment->save();

            if (!$wasSettled && $internalStatus === 'settled') {
                $registration = $payment->event_registration_id ? EventRegistration::find($payment->event_registration_id) : null;
                if ($registration && $registration->status !== 'active') {
                    $registration->status = 'active';
                    $registration->payment_verified_at = now();
                    $registration->payment_verified_by = null;
                    $registration->save();
                }
                $this->processEventReferralCommission($event, $payment);
                $this->notifyEventMidtransRegistrationSuccess($event, $payment);
            }

            if ($payment->event_registration_id && in_array($internalStatus, ['expired', 'rejected'], true)) {
                $registration = EventRegistration::find($payment->event_registration_id);
                if ($registration && $registration->status !== 'active' && $registration->status !== 'canceled') {
                    $registration->status = $internalStatus;
                    $registration->save();
                }
            }
            DB::commit();

            return response()->json([
                'message' => 'OK',
                'order_id' => $orderId,
                'status' => $payment->status,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Midtrans finalize failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal memeriksa status pembayaran.'], 500);
        }
    }

    /**
     * Query current pending order for this user+event.
     */
    public function pendingOrder(Request $request, Event $event): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payment = ManualPayment::query()
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('method', self::MIDTRANS_METHOD)
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        // Real-time sync: check actual status from Midtrans if there's a pending payment
        if ($payment && $payment->order_id) {
            try {
                $this->configureMidtrans();
                $midtransStatus = (array) \Midtrans\Transaction::status($payment->order_id);
                $actualStatus = $this->mapMidtransToInternalStatus(
                    $midtransStatus['transaction_status'] ?? null,
                    $midtransStatus['fraud_status'] ?? null
                );

                if ($actualStatus !== 'pending') {
                    $payment->status = $actualStatus;
                    $payment->save();

                    if (in_array($actualStatus, ['expired', 'rejected'], true)) {
                        $reg = EventRegistration::find($payment->event_registration_id);
                        if ($reg && !in_array($reg->status, ['active', 'canceled'], true)) {
                            $reg->status = $actualStatus;
                            $reg->save();
                        }
                    }

                    $payment = null; // treat as no pending payment
                }
            } catch (\Throwable $e) {
                // 404 = never charged → expired
                if (str_contains($e->getMessage(), '404') || str_contains(strtolower($e->getMessage()), 'not found')) {
                    $payment->status = 'expired';
                    $payment->save();
                    $reg = EventRegistration::find($payment->event_registration_id);
                    if ($reg && !in_array($reg->status, ['active', 'canceled'], true)) {
                        $reg->status = 'expired';
                        $reg->save();
                    }
                    $payment = null;
                }
                // Other errors: keep as pending, don't block the user
            }
        }

        // If registration is expired/rejected, force new regardless of payment status
        $registration = EventRegistration::query()
            ->where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->latest('id')
            ->first();

        $registrationExpired = $registration && in_array($registration->status, ['expired', 'rejected'], true);

        // If registration is expired, treat any pending payment as stale too
        if ($registrationExpired && $payment) {
            $payment->status = 'expired';
            $payment->save();
            $payment = null;
        }

        $hasExpiredRegistration = $registrationExpired && !$payment;

        return response()->json([
            'pending' => (bool) $payment,
            'order_id' => $payment?->order_id,
            'amount' => $payment ? (int) round((float) $payment->amount) : null,
            'snap_token' => $this->getSnapTokenFromPayment($payment),
            'whatsapp_number' => $payment?->whatsapp_number,
            'referral_code' => $payment?->referral_code,
            'needs_force_new' => $hasExpiredRegistration,
        ]);
    }

    /**
     * Query current pending order for this user+course.
     */
    public function coursePendingOrder(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payment = ManualPayment::query()
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->where('method', self::MIDTRANS_METHOD)
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        // Real-time sync: check actual status from Midtrans if there's a pending payment
        if ($payment && $payment->order_id) {
            try {
                $this->configureMidtrans();
                $midtransStatus = (array) \Midtrans\Transaction::status($payment->order_id);
                $actualStatus = $this->mapMidtransToInternalStatus(
                    $midtransStatus['transaction_status'] ?? null,
                    $midtransStatus['fraud_status'] ?? null
                );

                if ($actualStatus !== 'pending') {
                    $payment->status = $actualStatus;
                    $payment->save();

                    if (in_array($actualStatus, ['expired', 'rejected'], true) && $payment->enrollment_id) {
                        $enr = Enrollment::find($payment->enrollment_id);
                        if ($enr && $enr->status !== 'active') {
                            $enr->status = $actualStatus;
                            $enr->save();
                        }
                    }

                    $payment = null;
                }
            } catch (\Throwable $e) {
                if (str_contains($e->getMessage(), '404') || str_contains(strtolower($e->getMessage()), 'not found')) {
                    $payment->status = 'expired';
                    $payment->save();
                    if ($payment->enrollment_id) {
                        $enr = Enrollment::find($payment->enrollment_id);
                        if ($enr && $enr->status !== 'active') {
                            $enr->status = 'expired';
                            $enr->save();
                        }
                    }
                    $payment = null;
                }
                // Other errors: keep as pending, don't block the user
            }
        }

        // Check if there's an expired/rejected enrollment that needs force_new
        $hasExpiredEnrollment = false;
        if (!$payment) {
            $hasExpiredEnrollment = Enrollment::query()
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->whereIn('status', ['expired', 'rejected'])
                ->exists();
        }

        return response()->json([
            'pending' => (bool) $payment,
            'order_id' => $payment?->order_id,
            'amount' => $payment ? (int) round((float) $payment->amount) : null,
            'snap_token' => $this->getSnapTokenFromPayment($payment),
            'whatsapp_number' => $payment?->whatsapp_number,
            'referral_code' => $payment?->referral_code,
            'needs_force_new' => $hasExpiredEnrollment,
        ]);
    }

    /**
     * Refresh course payment status by orderId (auth recommended).
     */
    public function refreshCoursePayment(Request $request, string $orderId): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payment = ManualPayment::query()
            ->where('order_id', $orderId)
            ->where('user_id', $user->id)
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'Order tidak ditemukan.'], 404);
        }

        try {
            $this->configureMidtrans();
            $status = (array) \Midtrans\Transaction::status($orderId);
            $internalStatus = $this->mapMidtransToInternalStatus(
                $status['transaction_status'] ?? null,
                $status['fraud_status'] ?? null
            );

            DB::beginTransaction();
            $wasSettled = $payment->status === 'settled';
            $payment->status = $internalStatus;
            $payment->metadata = array_merge((array) ($payment->metadata ?? []), [
                'midtrans_status' => $status,
                'refreshed_at' => now()->toIso8601String(),
            ]);
            $payment->save();

            if (!$wasSettled && $internalStatus === 'settled') {
                if ($payment->enrollment_id) {
                    $enrollment = Enrollment::find($payment->enrollment_id);
                    if ($enrollment && $enrollment->status !== 'active') {
                        $enrollment->status = 'active';
                        $enrollment->save();
                    }
                }
                if ($payment->course_id) {
                    $course = Course::find($payment->course_id);
                    if ($course) {
                        $this->processCourseReferralCommission($course, $payment);
                    }
                }
            }

            // Update enrollment status for expired/rejected
            if (in_array($internalStatus, ['expired', 'rejected'], true) && $payment->enrollment_id) {
                $enrollment = Enrollment::find($payment->enrollment_id);
                if ($enrollment && $enrollment->status !== 'active') {
                    $enrollment->status = $internalStatus;
                    $enrollment->save();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'OK',
                'order_id' => $orderId,
                'status' => $payment->status,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Midtrans refreshCoursePayment failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal memeriksa status pembayaran.'], 500);
        }
    }

    /**
     * Optional: Core API QRIS generator endpoint.
     * Currently not used by the UI; return a safe response to avoid 500.
     */
    public function qrisCore(Request $request, Event $event): JsonResponse
    {
        return response()->json([
            'message' => 'QRIS Core API belum diaktifkan. Gunakan QRIS statis/manual upload.',
        ], 501);
    }
}
