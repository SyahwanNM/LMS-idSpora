<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\ManualPayment;
use App\Models\PaymentProof;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ManualPaymentController extends Controller
{
    public function register(Request $request, Event $event)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->back()->with('error', 'Harap login terlebih dahulu.');
        }

        $paymentMethod = $request->input('payment_method', 'midtrans');

        // Resolve price
        $attendanceType = strtolower(trim((string) $request->input('attendance_type', 'offline')));
        $isHybridEvent = !empty($event->maps_url) && !empty($event->zoom_link)
            && ($event->price_offline > 0 || $event->price_online > 0);

        if ($isHybridEvent) {
            $rawPrice = $attendanceType === 'online'
                ? (float) ($event->price_online ?? 0)
                : (float) ($event->price_offline ?? 0);
            $discountPct = (method_exists($event, 'hasDiscount') && $event->hasDiscount())
                ? (float) ($event->discount_percentage ?? 0) : 0.0;
            $finalPrice = $discountPct > 0 ? round($rawPrice * (1 - $discountPct / 100), 2) : $rawPrice;
        } else {
            $finalPrice = method_exists($event, 'hasDiscount') && $event->hasDiscount()
                ? ($event->discounted_price ?? $event->price)
                : ($event->price ?? 0);
        }

        // Apply reseller/referral discount if active
        $rawReferralCode = trim((string) $request->input('referral_code', ''));
        $referralCode = null;
        if ($rawReferralCode !== '' && (bool) ($event->is_reseller_event ?? false)) {
            $referrer = \App\Models\User::where('referral_code', $rawReferralCode)->first();
            if ($referrer && (int) $referrer->id !== (int) $user->id) {
                $referralCode = $rawReferralCode;
                $finalPrice = round($finalPrice * 0.90, 2);
            }
        }

        $isFree = (int) $finalPrice <= 0;

        // Free event → register immediately
        if ($isFree) {
            return app(\App\Http\Controllers\Admin\EventController::class)->register($request, $event);
        }

        // Midtrans → not handled here
        if ($paymentMethod === 'midtrans') {
            return redirect()->back()->with('error', 'Gunakan tombol Pay with Midtrans untuk pembayaran online.');
        }

        // ── Transfer Rekening ──────────────────────────────────────
        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,webp|max:1024',
        ], [
            'payment_proof.required' => 'Bukti transfer wajib diupload.',
            'payment_proof.max' => 'Ukuran file maksimal 1 MB.',
            'payment_proof.mimes' => 'Format file harus JPG, PNG, atau WebP.',
        ]);

        // Sync profile fields
        $profileUpdates = [];
        $fullName = $request->input('full_name') ?: $user->name;
        $email = $request->input('email') ?: $user->email;
        $phone = $request->input('whatsapp') ?: $user->phone;
        $university = $request->input('university_origin') ?: $user->institution;
        $position = $request->input('position') ?: $user->profession;

        if ($fullName !== $user->name)
            $profileUpdates['name'] = $fullName;
        if ($email !== $user->email)
            $profileUpdates['email'] = $email;
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

        DB::beginTransaction();
        try {
            // Create or reset registration
            $registration = EventRegistration::where('user_id', $user->id)
                ->where('event_id', $event->id)->first();

            $orderId = 'TRF-' . $user->id . '-' . $event->id . '-' . time();

            if (!$registration) {
                $registration = EventRegistration::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'status' => 'pending',
                    'registration_code' => $orderId,
                    'total_price' => $finalPrice,
                    'university_origin' => $request->input('university_origin'),
                    'study_program' => $request->input('study_program'),
                    'position' => $request->input('position'),
                    'referral_code' => $referralCode,
                ]);
            } else {
                $registration->update([
                    'status' => 'pending',
                    'total_price' => $finalPrice,
                    'rejection_reason' => null,
                    'university_origin' => $request->input('university_origin'),
                    'study_program' => $request->input('study_program'),
                    'position' => $request->input('position'),
                    'referral_code' => $referralCode,
                ]);
            }

            // Store proof file
            $file = $request->file('payment_proof');
            $path = $file->store('payments/transfer', 'public');

            $registration->update(['payment_proof' => $path]);

            // ManualPayment trace
            $manual = ManualPayment::where('event_registration_id', $registration->id)
                ->whereIn('status', ['pending', 'rejected'])->latest()->first();

            if (!$manual) {
                $manual = new ManualPayment();
                $manual->order_id = $orderId;
            }

            $manual->fill([
                'event_id' => $event->id,
                'event_registration_id' => $registration->id,
                'user_id' => $user->id,
                'amount' => $finalPrice,
                'currency' => 'IDR',
                'method' => 'manual_transfer',
                'status' => 'pending',
                'referral_code' => $referralCode,
                'metadata' => array_merge((array) ($manual->metadata ?? []), [
                    'source' => 'event',
                    'payment_method' => 'transfer',
                    'attendance_type' => $attendanceType,
                ]),
            ])->save();

            // PaymentProof record
            PaymentProof::create([
                'manual_payment_id' => $manual->id,
                'event_registration_id' => $registration->id,
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $user->id,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengirim bukti pembayaran: ' . $e->getMessage());
        }

        return redirect()->route('events.show', $event->id)
            ->with('success', 'Transfer proof has been successfully submitted. Your registration is under review by the admin.');
    }
}
