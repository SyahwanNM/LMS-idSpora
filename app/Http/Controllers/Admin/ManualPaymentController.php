<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\ManualPayment;
use App\Models\PaymentProof;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ManualPaymentController extends Controller
{
    private const REFERRAL_DISCOUNT_RATE = 0.10;

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
        return max(0, round($base * (1 - self::REFERRAL_DISCOUNT_RATE), 2));
    }

    public function checkReferral(Request $request, Event $event)
    {
        $user = $request->user();
        $code = trim((string) $request->query('code', ''));

        $finalPrice = method_exists($event, 'hasDiscount') && $event->hasDiscount()
            ? (float) ($event->discounted_price ?? $event->price)
            : (float) ($event->price ?? 0);

        if (!(bool) ($event->is_reseller_event ?? false)) {
            return response()->json([
                'valid' => false,
                'message' => 'Event ini tidak mendukung referral.',
                'base_amount' => (int) round($finalPrice),
                'discount_rate' => 0,
                'final_amount' => (int) round($finalPrice),
            ]);
        }

        $referrer = $this->resolveValidReferrer($user, $code);
        if (!$referrer) {
            if ($user && $code !== '' && $code === (string) ($user->referral_code ?? '')) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Kode referral tidak boleh menggunakan kode milik sendiri.',
                    'base_amount' => (int) round($finalPrice),
                    'discount_rate' => 0,
                    'final_amount' => (int) round($finalPrice),
                ]);
            }

            return response()->json([
                'valid' => false,
                'message' => $code === '' ? '' : 'Kode referral tidak ditemukan.',
                'base_amount' => (int) round($finalPrice),
                'discount_rate' => 0,
                'final_amount' => (int) round($finalPrice),
            ]);
        }

        $final = $this->applyReferralDiscountAmount($finalPrice, true);
        return response()->json([
            'valid' => true,
            'message' => 'Kode referral valid. Diskon 10% diterapkan.',
            'base_amount' => (int) round($finalPrice),
            'discount_rate' => self::REFERRAL_DISCOUNT_RATE,
            'final_amount' => (int) round($final),
        ]);
    }

    public function register(Request $request, Event $event)
    {
        $user = $request->user();
        if(!$user){ return redirect()->back()->with('error','Harap login terlebih dahulu.'); }

        $hasPendingMidtrans = ManualPayment::query()
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('method', 'midtrans')
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingMidtrans) {
            return redirect()->back()->with('warning', 'Anda memiliki pembayaran Midtrans yang masih pending. Silakan selesaikan pembayaran Midtrans terlebih dahulu.');
        }

        // If event is free, redirect to normal register
        $finalPrice = method_exists($event,'hasDiscount') && $event->hasDiscount() ? ($event->discounted_price ?? $event->price) : ($event->price ?? 0);
        $isFree = (int)$finalPrice <= 0;
        // If free, call existing register endpoint behavior
        if($isFree){
            return app(\App\Http\Controllers\Admin\EventController::class)->register($request, $event);
        }

        // Validate upload
        $request->validate([ 'payment_proof' => 'nullable|image|mimes:jpg,jpeg,png|max:5120' ]);

        // Referral/discount: only for reseller-enabled events.
        $rawReferralCode = trim((string) $request->input('referral_code'));
        $referrer = (bool) ($event->is_reseller_event ?? false)
            ? $this->resolveValidReferrer($user, $rawReferralCode)
            : null;
        $referralCode = $referrer ? $rawReferralCode : null;

        $finalAmount = $this->applyReferralDiscountAmount((float) $finalPrice, $referrer !== null);

        // Create or update pending registration
        $existing = EventRegistration::where('user_id', $user->id)->where('event_id', $event->id)->first();

        if (!$existing) {
            $existing = EventRegistration::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'status' => 'pending',
                'registration_code' => 'EVT-'.strtoupper(uniqid()),
                'total_price' => $finalAmount,
            ]);
            $msg = 'Pendaftaran terkirim; menunggu verifikasi admin.';
        } else {
            $existing->update([
                'status' => 'pending',
                'total_price' => $finalAmount,
            ]);
            $msg = 'Bukti pembayaran diperbarui; menunggu verifikasi admin.';
        }

        // Find existing pending or rejected manual payment
        $manual = ManualPayment::where('event_registration_id', $existing->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->orderBy('id', 'desc')
            ->first();

        if (!$manual) {
            $manual = new ManualPayment();
            $manual->order_id = 'MP-' . strtoupper(uniqid());
        }

        $manual->fill([
            'event_id' => $event->id,
            'event_registration_id' => $existing->id,
            'user_id' => $user->id,
            'amount' => $finalAmount,
            'currency' => 'IDR',
            'method' => 'qris',
            'status' => 'pending',
            'referral_code' => $referralCode,
        ])->save();

        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $path = $file->store('payments', 'public');

            // store proof record
            PaymentProof::create([
                'manual_payment_id' => $manual->id,
                'event_registration_id' => $existing->id,
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $user->id,
            ]);

            // keep legacy field for admin UI convenience
            $existing->payment_proof = $path;
            $existing->save();
        }

        return redirect()->route('events.show', $event->id)->with('success', $msg);
    }
}
