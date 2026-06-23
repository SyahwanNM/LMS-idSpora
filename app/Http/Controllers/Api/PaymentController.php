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
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Mail\PaymentInvoiceMail;

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
                'title' => 'Registration Confirmed',
                'message' => 'You have successfully registered for the event "' . ($event->title ?? 'Event') . '".',
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

    public function store(Request $request)
    {
        abort(403, 'Pembayaran manual dinonaktifkan. Gunakan Midtrans.');
    }

    /**
     * Update proof for an existing payment (using POST because of file upload).
     */
    public function update(Request $request, $id)
    {
        abort(403, 'Pembayaran manual dinonaktifkan. Gunakan Midtrans.');
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
            if ($registration) {
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

    private function markVoucherUsedIfApplicable(ManualPayment $payment): void
    {
        $redemptionId = data_get($payment->metadata, 'voucher_redemption_id');
        if ($redemptionId) {
            $redemption = VoucherRedemption::find($redemptionId);
            if ($redemption && !$redemption->is_used) {
                $redemption->update([
                    'is_used' => true,
                    'used_at' => now(),
                ]);
                Log::info('Voucher redemption marked as used', [
                    'redemption_id' => $redemption->id,
                    'code' => $redemption->code,
                    'payment_id' => $payment->id,
                ]);
            }
        }
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

        // Resolve base amount — for hybrid events use attendance_type to pick the right price
        $attendanceType = strtolower(trim((string) $request->query('attendance_type', $request->input('attendance_type', 'offline'))));
        $isHybridEvent = !empty($event->maps_url) && !empty($event->zoom_link)
            && ($event->price_offline > 0 || $event->price_online > 0);

        if ($isHybridEvent) {
            $rawHybridPrice = $attendanceType === 'online'
                ? (float) ($event->price_online ?? 0)
                : (float) ($event->price_offline ?? 0);
            $discountPct = (method_exists($event, 'hasDiscount') && $event->hasDiscount())
                ? (float) ($event->discount_percentage ?? 0) : 0.0;
            $baseAmount = $discountPct > 0
                ? round($rawHybridPrice * (1 - $discountPct / 100), 2)
                : $rawHybridPrice;
        } else {
            $hasDiscount = method_exists($event, 'hasDiscount') ? (bool) $event->hasDiscount() : false;
            $baseAmount = (float) ($hasDiscount ? ($event->discounted_price ?? $event->price) : ($event->price ?? 0));
        }

        if ($baseAmount <= 0) {
            return response()->json(['message' => 'Event gratis, tidak perlu Midtrans.'], 400);
        }

        $rawReferralCode = trim((string) $request->query('referral_code', $request->input('referral_code')));
        $referrer = (bool) ($event->is_reseller_event ?? false)
            ? $this->resolveValidReferrer($user, $rawReferralCode)
            : null;
        $referralCode = $referrer ? $rawReferralCode : null;
        $finalAmount = $this->applyReferralDiscountAmount($baseAmount, $referrer !== null);

        $voucherCode = trim((string) $request->query('voucher_code', $request->input('voucher_code')));
        $redemption = null;
        $discountAmount = 0.0;

        if ($voucherCode !== '') {
            $redemption = VoucherRedemption::where('user_id', $user->id)
                ->where('code', $voucherCode)
                ->first();

            if (!$redemption) {
                return response()->json(['message' => 'Voucher tidak ditemukan.'], 422);
            }

            if (!$redemption->isUsable()) {
                return response()->json(['message' => 'Voucher tidak valid atau sudah kedaluwarsa.'], 422);
            }

            $voucher = $redemption->voucher;
            if ($finalAmount < $voucher->min_purchase) {
                return response()->json([
                    'message' => 'Minimal pembelian untuk menggunakan voucher ini adalah Rp' . number_format($voucher->min_purchase, 0, ',', '.') . '.'
                ], 422);
            }

            $discountAmount = $voucher->calculateDiscount($finalAmount);
            $finalAmount = max(0.0, $finalAmount - $discountAmount);
        }

        $dial = trim((string) $request->query('dial_code', $request->input('dial_code')));
        $wa = trim((string) $request->query('whatsapp', $request->input('whatsapp')));
        $phone = trim($dial . $wa);
        if ($phone === '') {
            $phone = (string) ($user->phone ?? '');
        }

        // Sync profile fields
        $profileUpdates = [];
        $fullName = $request->query('full_name', $request->input('full_name')) ?: $user->name;
        $university = $request->query('university_origin', $request->input('university_origin')) ?: $user->institution;
        $position = $request->query('position', $request->input('position')) ?: $user->profession;

        if ($fullName !== $user->name)
            $profileUpdates['name'] = $fullName;
        if ($phone !== $user->phone)
            $profileUpdates['phone'] = $phone;
        if ($university !== $user->institution)
            $profileUpdates['institution'] = $university;
        if ($position !== $user->profession)
            $profileUpdates['profession'] = $position;

        if (!empty($profileUpdates)) {
            $user->update($profileUpdates);
            $user->refresh();
        }

        $regData = [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'university_origin' => $request->query('university_origin', $request->input('university_origin')),
            'study_program' => $request->query('study_program', $request->input('study_program')),
            'position' => $request->query('position', $request->input('position')),
            'full_name' => $fullName,
            'whatsapp_number' => $phone,
            'team_name' => $request->query('team_name', $request->input('team_name')),
            'institution_location' => $request->query('institution_location', $request->input('institution_location')),
            'info_source' => $request->query('info_source', $request->input('info_source')),
            'educational_background' => $request->query('educational_background', $request->input('educational_background')),
        ];

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

            if ($finalAmount <= 0) {
                if (!$registration) {
                    $registration = EventRegistration::create(array_merge($regData, [
                        'status' => 'active',
                        'registration_code' => 'EVT-' . strtoupper(uniqid()),
                        'total_price' => 0.00,
                        'payment_verified_at' => now(),
                    ]));
                } else {
                    $registration->fill(array_merge($regData, [
                        'status' => 'active',
                        'total_price' => 0.00,
                        'payment_verified_at' => now(),
                    ]))->save();
                }

                $method = $redemption ? 'voucher' : 'free';
                $orderId = 'VCH-EVT-' . strtoupper(uniqid());

                $payment = ManualPayment::create([
                    'event_id' => $event->id,
                    'event_registration_id' => $registration->id,
                    'user_id' => $user->id,
                    'order_id' => $orderId,
                    'amount' => 0,
                    'currency' => 'IDR',
                    'method' => $method,
                    'status' => 'settled',
                    'whatsapp_number' => $phone ?: null,
                    'referral_code' => $referralCode,
                    'metadata' => [
                        'source' => 'event',
                        'type' => 'voucher_free',
                        'base_amount' => $baseAmount,
                        'voucher_code' => $voucherCode ?: null,
                        'voucher_redemption_id' => $redemption?->id ?? null,
                        'voucher_discount' => $discountAmount,
                        'attendance_type' => $attendanceType,
                    ]
                ]);

                if ($redemption) {
                    $redemption->update([
                        'is_used' => true,
                        'used_at' => now(),
                    ]);
                }

                try {
                    $pointsService = app(\App\Services\UserPointsService::class);
                    $pointsService->addEventPoints($user, $event, $registration);
                } catch (\Throwable $e) {
                    Log::error('Error awarding event points: ' . $e->getMessage());
                }

                try {
                    UserNotification::create([
                        'user_id' => $user->id,
                        'type' => 'event_registration',
                        'title' => 'Registration Confirmed',
                        'message' => 'Registration for "' . $event->title . '" has been confirmed.',
                        'data' => ['url' => route('events.show', $event)],
                        'expires_at' => now()->addDays(14),
                    ]);
                } catch (\Throwable $e) { /* ignore */
                }

                DB::commit();

                return response()->json([
                    'redirect_url' => route('events.registered.detail', $event->id),
                    'amount' => 0,
                    'message' => 'Pendaftaran berhasil menggunakan voucher.'
                ]);
            }

            if (!$registration) {
                $registration = EventRegistration::create(array_merge($regData, [
                    'status' => 'pending',
                    'registration_code' => 'EVT-' . strtoupper(uniqid()),
                    'total_price' => $finalAmount,
                ]));
            } else {
                $registration->fill(array_merge($regData, [
                    'status' => 'pending',
                    'total_price' => $finalAmount,
                ]))->save();
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
                $paymentVoucherCode = data_get($payment->metadata, 'voucher_code');
                $paymentReferral = $payment->referral_code;
                if ($paymentVoucherCode !== $voucherCode || $paymentReferral !== $referralCode) {
                    $forceNew = true;
                }
            }

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
                    'attendance_type' => $attendanceType,
                    'voucher_code' => $voucherCode ?: null,
                    'voucher_redemption_id' => $redemption?->id ?? null,
                    'voucher_discount' => $discountAmount,
                ]),
            ]);
            $payment->save();

            $grossAmount = (int) round($finalAmount);
            $snapParams = $this->buildMidtransSnapParams(
                (string) $payment->order_id,
                $grossAmount,
                [
                    [
                        'id' => 'event-' . $event->id,
                        'price' => $grossAmount,
                        'quantity' => 1,
                        'name' => (string) ($event->title ?? 'Event'),
                    ]
                ],
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
                            'voucher_code' => $voucherCode ?: null,
                            'voucher_redemption_id' => $redemption?->id ?? null,
                            'voucher_discount' => $discountAmount,
                        ],
                    ]);
                    $newPayment->save();

                    $grossAmount = (int) round($finalAmount);
                    $snapParams = $this->buildMidtransSnapParams(
                        (string) $newPayment->order_id,
                        $grossAmount,
                        [
                            [
                                'id' => 'event-' . $event->id,
                                'price' => $grossAmount,
                                'quantity' => 1,
                                'name' => (string) ($event->title ?? 'Event'),
                            ]
                        ],
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

        $voucherCode = trim((string) $request->query('voucher_code', $request->input('voucher_code')));
        $redemption = null;
        $discountAmount = 0.0;

        if ($voucherCode !== '') {
            $redemption = VoucherRedemption::where('user_id', $user->id)
                ->where('code', $voucherCode)
                ->first();

            if (!$redemption) {
                return response()->json(['message' => 'Voucher tidak ditemukan.'], 422);
            }

            if (!$redemption->isUsable()) {
                return response()->json(['message' => 'Voucher tidak valid atau sudah kedaluwarsa.'], 422);
            }

            $voucher = $redemption->voucher;
            if ($finalAmount < $voucher->min_purchase) {
                return response()->json([
                    'message' => 'Minimal pembelian untuk menggunakan voucher ini adalah Rp' . number_format($voucher->min_purchase, 0, ',', '.') . '.'
                ], 422);
            }

            $discountAmount = $voucher->calculateDiscount($finalAmount);
            $finalAmount = max(0.0, $finalAmount - $discountAmount);
        }

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

            if ($finalAmount <= 0) {
                $enrollment->status = 'active';
                $enrollment->save();

                $method = $redemption ? 'voucher' : 'free';
                $orderId = 'VCH-CRS-' . strtoupper(uniqid());

                $payment = ManualPayment::create([
                    'course_id' => $course->id,
                    'enrollment_id' => $enrollment->id,
                    'user_id' => $user->id,
                    'order_id' => $orderId,
                    'amount' => 0,
                    'currency' => 'IDR',
                    'method' => $method,
                    'status' => 'settled',
                    'whatsapp_number' => $phone ?: null,
                    'referral_code' => $referralCode,
                    'metadata' => [
                        'source' => 'course',
                        'type' => 'voucher_free',
                        'base_amount' => $baseAmount,
                        'voucher_code' => $voucherCode ?: null,
                        'voucher_redemption_id' => $redemption?->id ?? null,
                        'voucher_discount' => $discountAmount,
                    ]
                ]);

                if ($redemption) {
                    $redemption->update([
                        'is_used' => true,
                        'used_at' => now(),
                    ]);
                }

                $enrollment->checkAndComplete($user);

                DB::commit();

                return response()->json([
                    'redirect_url' => route('course.learn', $course->id),
                    'amount' => 0,
                    'message' => 'Pendaftaran berhasil menggunakan voucher.'
                ]);
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
                $paymentVoucherCode = data_get($payment->metadata, 'voucher_code');
                $paymentReferral = $payment->referral_code;
                if ($paymentVoucherCode !== $voucherCode || $paymentReferral !== $referralCode) {
                    $forceNew = true;
                }
            }

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
                    'voucher_code' => $voucherCode ?: null,
                    'voucher_redemption_id' => $redemption?->id ?? null,
                    'voucher_discount' => $discountAmount,
                ]),
            ]);
            $payment->save();

            $grossAmount = (int) round($finalAmount);
            $snapParams = $this->buildMidtransSnapParams(
                (string) $payment->order_id,
                $grossAmount,
                [
                    [
                        'id' => 'course-' . $course->id,
                        'price' => $grossAmount,
                        'quantity' => 1,
                        'name' => (string) ($course->name ?? 'Course'),
                    ]
                ],
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
                            'voucher_code' => $voucherCode ?: null,
                            'voucher_redemption_id' => $redemption?->id ?? null,
                            'voucher_discount' => $discountAmount,
                        ],
                    ]);
                    $newPayment->save();

                    $grossAmount = (int) round($finalAmount);
                    $snapParams = $this->buildMidtransSnapParams(
                        (string) $newPayment->order_id,
                        $grossAmount,
                        [
                            [
                                'id' => 'course-' . $course->id,
                                'price' => $grossAmount,
                                'quantity' => 1,
                                'name' => (string) ($course->name ?? 'Course'),
                            ]
                        ],
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
                $this->markVoucherUsedIfApplicable($payment);
                // Activate related entities
                if ($payment->event_registration_id) {
                    $registration = EventRegistration::find($payment->event_registration_id);
                    if ($registration) {
                        $isStage2 = (data_get($payment->metadata, 'stage') == 2 || str_starts_with($payment->order_id, 'STG2-'));
                        if ($isStage2) {
                            $registration->stage2_payment_status = 'settled';
                            $registration->stage2_payment_at = now();
                            $registration->save();
                        } else {
                            if ($registration->status !== 'active') {
                                $registration->status = 'active';
                                $registration->payment_verified_at = now();
                                $registration->payment_verified_by = null;
                                $registration->save();
                            }
                        }
                    }

                    $event = $payment->event_id ? Event::find($payment->event_id) : null;
                    if ($event) {
                        $isStage2 = (data_get($payment->metadata, 'stage') == 2 || str_starts_with($payment->order_id, 'STG2-'));
                        if (!$isStage2) {
                            $this->processEventReferralCommission($event, $payment);
                        }
                        $this->notifyEventMidtransRegistrationSuccess($event, $payment);
                        $this->sendPaymentInvoice($payment, 'event', (string) ($event->title ?? 'Event'));
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
                        $this->processCourseTrainerRevenue($course, $payment);
                        $this->sendPaymentInvoice($payment, 'course', (string) ($course->name ?? 'Course'));
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
                $this->markVoucherUsedIfApplicable($payment);
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
                        $this->sendPaymentInvoice($payment, 'event', (string) ($event->title ?? 'Event'));
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
                        $this->sendPaymentInvoice($payment, 'course', (string) ($course->name ?? 'Course'));
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
            try {
                $status = (array) \Midtrans\Transaction::status($orderId);
                $internalStatus = $this->mapMidtransToInternalStatus(
                    $status['transaction_status'] ?? null,
                    $status['fraud_status'] ?? null
                );
            } catch (\Throwable $statusException) {
                $is404 = str_contains($statusException->getMessage(), '404')
                    || str_contains(strtolower($statusException->getMessage()), 'not found');

                if ($is404) {
                    $tokenCreatedAt = data_get($payment->metadata, 'snap_token_created_at') ?: $payment->created_at;
                    $tokenAgeMinutes = $tokenCreatedAt
                        ? abs(now()->diffInMinutes(\Carbon\Carbon::parse($tokenCreatedAt)))
                        : 0;

                    if ($tokenAgeMinutes >= 5) {
                        $internalStatus = 'expired';
                        $status = ['transaction_status' => 'expire', 'fraud_status' => null];
                    } else {
                        $internalStatus = 'pending';
                        $status = ['transaction_status' => 'pending', 'fraud_status' => null];
                    }
                } else {
                    throw $statusException;
                }
            }

            DB::beginTransaction();
            $wasSettled = $payment->status === 'settled';
            $payment->status = $internalStatus;
            $payment->metadata = array_merge((array) ($payment->metadata ?? []), [
                'midtrans_status' => $status,
                'refreshed_at' => now()->toIso8601String(),
            ]);
            $payment->save();

            if (!$wasSettled && $internalStatus === 'settled') {
                $this->markVoucherUsedIfApplicable($payment);
                $registration = $payment->event_registration_id ? EventRegistration::find($payment->event_registration_id) : null;
                if ($registration && $registration->status !== 'active') {
                    $registration->status = 'active';
                    $registration->payment_verified_at = now();
                    $registration->payment_verified_by = null;
                    $registration->save();
                }
                $this->processEventReferralCommission($event, $payment);
                $this->notifyEventMidtransRegistrationSuccess($event, $payment);
                $this->sendPaymentInvoice($payment, 'event', (string) ($event->title ?? 'Event'));
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
                // 404 = order not yet charged (user hasn't opened Snap popup yet) — keep as pending.
                // Only expire if snap token is older than expiry duration.
                if (str_contains($e->getMessage(), '404') || str_contains(strtolower($e->getMessage()), 'not found')) {
                    $tokenCreatedAt = data_get($payment->metadata, 'snap_token_created_at') ?: $payment->created_at;
                    $tokenAgeMinutes = $tokenCreatedAt
                        ? abs(now()->diffInMinutes(\Carbon\Carbon::parse($tokenCreatedAt)))
                        : 0;

                    if ($tokenAgeMinutes >= 5) {
                        // Token truly expired — mark as expired
                        $payment->status = 'expired';
                        $payment->save();
                        $reg = EventRegistration::find($payment->event_registration_id);
                        if ($reg && !in_array($reg->status, ['active', 'canceled'], true)) {
                            $reg->status = 'expired';
                            $reg->save();
                        }
                        $payment = null;
                    }
                    // else: token still valid, keep as pending — do nothing
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

        // Only treat registration as expired if Midtrans confirmed it — not just because
        // the snap token hasn't been used yet.
        $registrationExpired = $registration && in_array($registration->status, ['expired', 'rejected'], true);

        // If registration is truly expired and there's still a pending payment, expire it too
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
                    // 404 = order not yet charged (user hasn't opened Snap popup yet) — keep as pending.
                    // Only expire if snap token is older than expiry duration.
                    $tokenCreatedAt = data_get($payment->metadata, 'snap_token_created_at') ?: $payment->created_at;
                    $tokenAgeMinutes = $tokenCreatedAt
                        ? abs(now()->diffInMinutes(\Carbon\Carbon::parse($tokenCreatedAt)))
                        : 0;

                    if ($tokenAgeMinutes >= 5) {
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
                    // else: token still valid, keep as pending
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
            try {
                $status = (array) \Midtrans\Transaction::status($orderId);
                $internalStatus = $this->mapMidtransToInternalStatus(
                    $status['transaction_status'] ?? null,
                    $status['fraud_status'] ?? null
                );
            } catch (\Throwable $statusException) {
                $is404 = str_contains($statusException->getMessage(), '404')
                    || str_contains(strtolower($statusException->getMessage()), 'not found');

                if ($is404) {
                    $tokenCreatedAt = data_get($payment->metadata, 'snap_token_created_at') ?: $payment->created_at;
                    $tokenAgeMinutes = $tokenCreatedAt
                        ? abs(now()->diffInMinutes(\Carbon\Carbon::parse($tokenCreatedAt)))
                        : 0;

                    if ($tokenAgeMinutes >= 5) {
                        $internalStatus = 'expired';
                        $status = ['transaction_status' => 'expire', 'fraud_status' => null];
                    } else {
                        $internalStatus = 'pending';
                        $status = ['transaction_status' => 'pending', 'fraud_status' => null];
                    }
                } else {
                    throw $statusException;
                }
            }

            DB::beginTransaction();
            $wasSettled = $payment->status === 'settled';
            $payment->status = $internalStatus;
            $payment->metadata = array_merge((array) ($payment->metadata ?? []), [
                'midtrans_status' => $status,
                'refreshed_at' => now()->toIso8601String(),
            ]);
            $payment->save();

            if (!$wasSettled && $internalStatus === 'settled') {
                $this->markVoucherUsedIfApplicable($payment);
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
                        $this->sendPaymentInvoice($payment, 'course', (string) ($course->name ?? 'Course'));
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

    /**
     * Send a payment invoice email to the user — call once per settlement.
     * Idempotency: checks metadata flag so duplicate calls are no-ops.
     */
    private function sendPaymentInvoice(ManualPayment $payment, string $itemType, string $itemTitle): void
    {
        // Idempotency guard: skip if invoice already sent for this payment
        if (data_get($payment->metadata, 'invoice_sent')) {
            return;
        }

        try {
            $invoiceUser = $payment->user ?? User::find($payment->user_id);
            if (!$invoiceUser || empty($invoiceUser->email)) {
                return;
            }

            $prefix = $itemType === 'event' ? 'INV-EVT-' : 'INV-CRS-';
            $invoiceNumber = $prefix . strtoupper(substr(md5($payment->id . $payment->order_id), 0, 8));

            Mail::to($invoiceUser->email)->send(new PaymentInvoiceMail(
                invoiceNumber: $invoiceNumber,
                userName: (string) ($invoiceUser->name ?? 'User'),
                userEmail: (string) ($invoiceUser->email),
                itemType: $itemType,
                itemTitle: $itemTitle,
                amount: (float) ($payment->amount ?? 0),
                paymentMethod: (string) ($payment->method ?? 'midtrans'),
                paidAt: now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB',
                orderId: (string) ($payment->order_id ?? '-'),
            ));

            // Mark invoice as sent so we don't resend on duplicate webhook calls
            $payment->metadata = array_merge((array) ($payment->metadata ?? []), [
                'invoice_sent' => true,
                'invoice_sent_at' => now()->toIso8601String(),
                'invoice_number' => $invoiceNumber,
            ]);
            $payment->save();

            Log::info('PaymentInvoice sent', ['order_id' => $payment->order_id, 'invoice' => $invoiceNumber]);
        } catch (\Throwable $e) {
            Log::warning('PaymentInvoice send failed', [
                'order_id' => $payment->order_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function processCourseTrainerRevenue(Course $course, ManualPayment $payment): void
    {
        if ($course->trainer_id && $course->trainer_revenue_percent > 0) {
            $trainer = User::find($course->trainer_id);
            if ($trainer) {
                $trainerShare = ($payment->amount * $course->trainer_revenue_percent) / 100;
                if ($trainerShare > 0) {
                    $trainer->increment('wallet_balance', $trainerShare);

                    \App\Models\TrainerNotification::create([
                        'trainer_id' => $trainer->id,
                        'type' => 'revenue_share',
                        'title' => 'Pendapatan Course Baru',
                        'message' => 'Anda menerima bagi hasil sebesar Rp ' . number_format($trainerShare, 0, ',', '.') . ' dari penjualan course: ' . $course->name,
                        'data' => ['amount' => $trainerShare, 'course_id' => $course->id]
                    ]);
                }
            }
        }
    }

    public function downloadInvoice(Request $request, string $orderId)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Find the payment. Admins can view any payment. Regular users can only view their own.
        $paymentQuery = ManualPayment::where('order_id', $orderId);
        if ($user->role !== 'admin') {
            $paymentQuery->where('user_id', $user->id);
        }
        $payment = $paymentQuery->first();

        if (!$payment) {
            abort(404, 'Transaksi tidak ditemukan.');
        }

        // Determine item details
        $itemType = $payment->event_id ? 'event' : 'course';
        $itemTitle = '';
        if ($itemType === 'event') {
            $event = Event::find($payment->event_id);
            $itemTitle = $event ? $event->title : 'Event';
        } else {
            $course = Course::find($payment->course_id);
            $itemTitle = $course ? $course->name : 'Course';
        }

        $invoiceUser = $payment->user ?? User::find($payment->user_id);
        $prefix = $itemType === 'event' ? 'INV-EVT-' : 'INV-CRS-';
        $invoiceNumber = $prefix . strtoupper(substr(md5($payment->id . $payment->order_id), 0, 8));

        // Get logo base64 for embedding in PDF
        $logoPath = public_path('aset/logo idspora_dark.png');
        $logoSrc = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:image/png;base64,' . $logoData;
        }

        // Format dates
        $paidAt = $payment->updated_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB';

        // Prepare data
        $data = [
            'invoiceNumber' => $invoiceNumber,
            'userName' => $invoiceUser ? $invoiceUser->name : 'User',
            'userEmail' => $invoiceUser ? $invoiceUser->email : '',
            'itemType' => $itemType,
            'itemTitle' => $itemTitle,
            'amount' => (float) $payment->amount,
            'paymentMethod' => $payment->method ?? 'midtrans',
            'paidAt' => $paidAt,
            'orderId' => $payment->order_id,
            'logoSrc' => $logoSrc,
            'isPdf' => true,
        ];

        // Generate PDF using Dompdf
        $html = view('emails.payment-invoice', $data)->render();

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Invoice-' . $invoiceNumber . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
