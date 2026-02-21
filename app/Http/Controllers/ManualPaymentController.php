<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\ManualPayment;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ManualPaymentController extends Controller
{
    public function register(Request $request, Event $event)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->back()->with('error', 'Harap login terlebih dahulu.');
        }

        // If event is free, redirect to normal register
        $finalPrice = method_exists($event, 'hasDiscount') && $event->hasDiscount() ? ($event->discounted_price ?? $event->price) : ($event->price ?? 0);
        $isFree = (int)$finalPrice <= 0;
        // If free, call existing register endpoint behavior
        if ($isFree) {
            return app(\App\Http\Controllers\EventController::class)->register($request, $event);
        }

        // Validate upload
        $request->validate(['payment_proof' => 'nullable|image|mimes:jpg,jpeg,png|max:5120']);


        // LOGIKA REFERRAL
        // 1. Cek kode dari Input (prioritas) atau Cookie (cadangan)
        $referralCode = $request->input('referral_code') ?? $request->cookie('referral_code');
        $resellerId = null;

        // 2. Jika kode ada, cari pemiliknya (Reseller)
        if ($referralCode) {
            $reseller = User::where('referral_code', $referralCode)->first();

            // Pastikan reseller ketemu & user tidak mereferensikan dirinya sendiri
            if ($reseller && $reseller->id !== $user->id) {
                $resellerId = $reseller->id;

                // Diskon persentase (misal diskon 10%)
                $discountAmount = $finalPrice * 0.10;
                $finalPrice = $finalPrice - $discountAmount;
            }
        }

        // Create or update pending registration
        $existing = EventRegistration::where('user_id', $user->id)->where('event_id', $event->id)->first();

        if (!$existing) {
            $existing = EventRegistration::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'status' => 'pending',
                'registration_code' => 'EVT-' . strtoupper(uniqid()),
                'total_price' => $finalPrice,
                'reseller_id' => $resellerId,
            ]);
            $msg = 'Pendaftaran terkirim; menunggu verifikasi admin.';
        } else {
            $existing->update([
                'status' => 'pending',
                'total_price' => $finalPrice,
            ]);
            $msg = 'Bukti pembayaran diperbarui; menunggu verifikasi admin.';
        }

        // Create manual payment record
        $manual = ManualPayment::create([
            'event_id' => $event->id,
            'event_registration_id' => $existing->id,
            'user_id' => $user->id,
            'amount' => $finalPrice,
            'currency' => 'IDR',
            'method' => 'qris',
            'status' => 'pending',
        ]);

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
