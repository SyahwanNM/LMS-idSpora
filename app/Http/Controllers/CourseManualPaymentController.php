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
        $manualPayment->amount = (float) ($course->price ?? 0);
        $manualPayment->currency = 'IDR';
        $manualPayment->method = 'qris';
        $manualPayment->status = 'pending';
        $manualPayment->whatsapp_number = $whatsapp;
        $manualPayment->referral_code = $referralCode;
        if (!$manualPayment->order_id) {
            $manualPayment->order_id = 'MP-' . strtoupper(uniqid());
        }
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

        if ($manualPayment->status !== 'settled') {
            $manualPayment->status = 'settled'; // approved
            $manualPayment->save();

            // Process Referral Commission (10%)
            if (!empty($manualPayment->referral_code)) {
                $referrer = \App\Models\User::where('referral_code', $manualPayment->referral_code)->first();
                if ($referrer && $referrer->id !== $manualPayment->user_id) {
                    $commissionAmount = $manualPayment->amount * 0.10; // 10% commission
                    
                    // Prevent duplicate commission for the same payment
                    $existingReferral = \App\Models\Referral::where('user_id', $referrer->id)
                        ->where('referred_user_id', $manualPayment->user_id)
                        ->where('description', 'Komisi Course: ' . $course->name)
                        ->first();

                    if (!$existingReferral && $commissionAmount > 0) {
                        \App\Models\Referral::create([
                            'user_id' => $referrer->id,
                            'referred_user_id' => $manualPayment->user_id,
                            'amount' => $commissionAmount,
                            'status' => 'paid',
                            'description' => 'Komisi Course: ' . $course->name
                        ]);

                        $referrer->increment('wallet_balance', $commissionAmount);
                    }
                }
            }
        }

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

    public function freeEnroll(Request $request, Course $course): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $isFree = (int) ($course->price ?? 0) <= 0;
        if (!$isFree) {
            return redirect()->route('course.payment', $course->id)->with('error', 'Course ini berbayar.');
        }

        $enrollment = Enrollment::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['status' => 'active']
        );

        // Track in Finance (Amount 0)
        ManualPayment::create([
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
            'user_id' => $user->id,
            'order_id' => 'FREE-CRS-' . strtoupper(uniqid()),
            'amount' => 0,
            'currency' => 'IDR',
            'method' => 'free',
            'status' => 'settled',
            'metadata' => ['source' => 'course', 'type' => 'free']
        ]);

        return redirect()->route('course.learn', $course->id)->with('success', 'Pendaftaran berhasil!');
    }

    private function assertPaymentBelongsToCourse(Course $course, ManualPayment $manualPayment): void
    {
        if ((int) $manualPayment->course_id !== (int) $course->id) {
            abort(404);
        }
    }
}
