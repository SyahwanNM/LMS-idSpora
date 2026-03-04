<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\ManualPayment;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\Referral; 

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
        // Cek kode dari Input (prioritas) atau Cookie (cadangan)
        $referralCode = $request->input('referral_code') ?? $request->cookie('referral_code');
        $resellerId = null;

        // Kalau kode ada, cari pemiliknya (Reseller)
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
            'amount' => $finalPrice,
            'currency' => 'IDR',
            'method' => 'qris',
            'status' => 'pending',
            'referral_code' => $request->input('referral_code')
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
        if ($resellerId) {
            $cekReferral = Referral::where('referred_user_id', $user->id)
                ->where('event_id', $event->id)
                ->first();

            if (!$cekReferral) {
                // Cari data reseller untuk tahu levelnya
                $resellerData = User::find($resellerId);
                
                // Hitung total referral si reseller
                $totalReferrals = $resellerData ? $resellerData->referrals()->count() : 0;
                
                // Tentukan persentase berdasarkan level
                $persentaseKomisi = 0.10; // Default Bronze (10%)
                if ($totalReferrals >= 151) {
                    $persentaseKomisi = 0.15; // Gold (15%)
                } elseif ($totalReferrals >= 51) {
                    $persentaseKomisi = 0.12; // Silver (12%)
                }
                
                // Hitung nominal komisi fix
                // Catatan: Asumsi komisi dihitung dari harga dasar ($event->price)
                $komisiFix = ($event->price ?? 0) * $persentaseKomisi;

                Referral::create([
                    'user_id' => $resellerId,
                    'referred_user_id' => $user->id, 
                    'event_id' => $event->id, 
                    'amount' => $komisiFix, // Komisi dinamis sesuai level
                    'status' => 'pending',
                    'description' => 'Pembelian Event: ' . $event->title // Lebih spesifik
                ]);
            } else {
                // Kalp user upload ulang bukti (misal habis di-reject admin),
                // mastiin status komisinya balik jadi pending
                $cekReferral->update(['status' => 'pending']);
            }
        }

        return redirect()->route('events.show', $event->id)->with('success', $msg);
    }
}
