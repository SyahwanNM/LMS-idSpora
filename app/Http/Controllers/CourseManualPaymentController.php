<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ManualPayment;
use App\Models\PaymentProof;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseManualPaymentController extends Controller
{
    public function upload(Request $request, Course $course): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'whatsapp' => 'nullable|string|max:32',
            'referral_code' => 'nullable|string|max:64',
        ]);

        $whatsapp = isset($validated['whatsapp']) ? trim((string) $validated['whatsapp']) : null;
        $referralCode = isset($validated['referral_code']) ? trim((string) $validated['referral_code']) : null;

        // --- TAMBAHAN LOGIKA DISKON REFERRAL ---
        $reseller = null;
        $finalPrice = (float) ($course->price ?? 0);

        if ($referralCode) {
            $reseller = \App\Models\User::where('referral_code', $referralCode)->first();
            
            // Mastiin reseller ada dan bukan mendaftarkan dirinya sendiri
            if ($reseller && $reseller->id !== $user->id) {
                $discount = $finalPrice * 0.10;
                $finalPrice = max(0, $finalPrice - $discount);
            } else {
                // Reset jika kode tidak valid atau dipakai sendiri
                $referralCode = null; 
            }
        }

        $enrollment = Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['status' => 'pending']
        );

        $manualPayment = ManualPayment::query()
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->latest('id')
            ->first();

        if (!$manualPayment) {
            $manualPayment = new ManualPayment();
        }

        $manualPayment->course_id = $course->id;
        $manualPayment->enrollment_id = $enrollment->id;
        $manualPayment->user_id = $user->id;
        $manualPayment->amount = $finalPrice;
        $manualPayment->currency = 'IDR';
        $manualPayment->method = 'qris';
        $manualPayment->status = 'pending';
        $manualPayment->whatsapp_number = $whatsapp;
        $manualPayment->referral_code = $referralCode;
        $manualPayment->metadata = array_merge((array) ($manualPayment->metadata ?? []), [
            'source' => 'course',
        ]);
        $manualPayment->save();

        $file = $request->file('payment_proof');
        $storedPath = $file->store('payments', 'public');

        PaymentProof::create([
            'manual_payment_id' => $manualPayment->id,
            'event_registration_id' => null,
            'file_path' => $storedPath,
            'mime_type' => $file->getMimeType(),
            'file_size' => (int) $file->getSize(),
            'uploaded_by' => $user->id,
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }

    public function approve(Request $request, Course $course, ManualPayment $manualPayment): RedirectResponse
    {
        $this->assertPaymentBelongsToCourse($course, $manualPayment);

        // --- LOGIKA KOMISI RESELLER ---
        // cek dulu sebelum statusnya diubah jadi settled untuk mencegah double insert
        if ($manualPayment->status !== 'settled' && $manualPayment->referral_code) {
            $reseller = \App\Models\User::where('referral_code', $manualPayment->referral_code)->first();
            
            if ($reseller) {
                // Hitung total referral si reseller saat ini untuk menentukan Level
                $totalReferrals = $reseller->referrals()->count();
                
                // Nentuin persentase komisi berdasarkan Level
                if ($totalReferrals >= 151) {
                    $percentage = 0.15; // Gold: 15%
                } elseif ($totalReferrals >= 51) {
                    $percentage = 0.12; // Silver: 12%
                } else {
                    $percentage = 0.10; // Bronze: 10%
                }

                // Hitung nominal komisi dari harga yang dibayar user
                $commissionAmount = $manualPayment->amount * $percentage;

                // Catat komisi ke tabel Referrals
                \App\Models\Referral::create([
                    'user_id' => $reseller->id, // ID pemilik kode (reseller)
                    'referred_user_id' => $manualPayment->user_id, // ID user yg beli course
                    'amount' => $commissionAmount, // Nominal komisi dinamis
                    'status' => 'paid', // Langsung dianggap cair/paid
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Tambah saldo dompet (wallet) reseller
                $reseller->wallet_balance = $reseller->wallet_balance + $commissionAmount;
                $reseller->save();
            }
        }

        $manualPayment->status = 'settled'; // approved
        $manualPayment->save();

        if ($manualPayment->enrollment) {
            $manualPayment->enrollment->status = 'active';
            $manualPayment->enrollment->save();
        }

        return back()->with('success', 'Pembayaran berhasil di-approve.');
    }

    public function reject(Request $request, Course $course, ManualPayment $manualPayment): RedirectResponse
    {
        $this->assertPaymentBelongsToCourse($course, $manualPayment);

        $manualPayment->status = 'rejected';
        $manualPayment->save();

        if ($manualPayment->enrollment) {
            $manualPayment->enrollment->status = 'canceled';
            $manualPayment->enrollment->save();
        }

        return back()->with('success', 'Pembayaran berhasil di-reject.');
    }

    public function pending(Request $request, Course $course, ManualPayment $manualPayment): RedirectResponse
    {
        $this->assertPaymentBelongsToCourse($course, $manualPayment);

        $manualPayment->status = 'pending';
        $manualPayment->save();

        if ($manualPayment->enrollment) {
            $manualPayment->enrollment->status = 'pending';
            $manualPayment->enrollment->save();
        }

        return back()->with('success', 'Status pembayaran diubah ke pending.');
    }

    private function assertPaymentBelongsToCourse(Course $course, ManualPayment $manualPayment): void
    {
        if ((int) $manualPayment->course_id !== (int) $course->id) {
            abort(404);
        }
    }
}
