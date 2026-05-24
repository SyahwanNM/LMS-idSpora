<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ManualPayment;
use App\Models\PaymentProof;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentInvoiceMail;

class CourseManualPaymentController extends Controller
{
    private const REJECTION_REASONS = [
        'Nominal pembayaran kurang',
        'Nominal pembayaran lebih',
        'Gambar bukti pembayaran blur/buram. Silahkan kirim ulang',
        'Pembayaran dinyatakan tidak valid',
    ];

    public function upload(Request $request, Course $course): RedirectResponse
    {
        abort(403, 'Pembayaran manual tidak diaktifkan. Gunakan Midtrans.');
    }

    public function approve(Request $request, Course $course, ManualPayment $manualPayment): RedirectResponse
    {
        abort(403, 'Persetujuan pembayaran manual dinonaktifkan. Gunakan Midtrans.');
    }

    public function reject(Request $request, Course $course, ManualPayment $manualPayment): RedirectResponse
    {
        abort(403, 'Penolakan pembayaran manual dinonaktifkan. Gunakan Midtrans.');
    }

    public function pending(Request $request, Course $course, ManualPayment $manualPayment): RedirectResponse
    {
        abort(403, 'Perubahan status pembayaran manual dinonaktifkan. Gunakan Midtrans.');
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

        return redirect()->route('course.learn', $course->id)->with('success', 'Enroll Successfully!');
    }

    public function checkReferral(Request $request, Course $course)
    {
        $code = trim((string) $request->get('code'));
        if ($code === '') {
            return response()->json(['valid' => false, 'message' => '']);
        }

        $referrer = \App\Models\User::where('referral_code', $code)->first();
        if (!$referrer) {
            return response()->json(['valid' => false, 'message' => 'Kode referral tidak ditemukan.']);
        }

        if (Auth::check() && $referrer->id === Auth::id()) {
            return response()->json(['valid' => false, 'message' => 'Tidak bisa menggunakan kode sendiri.']);
        }

        $baseAmount = (float) ($course->hasDiscount() ? $course->discounted_price : ($course->price ?? 0));
        $discountedAmount = $baseAmount * 0.9; // 10% discount

        return response()->json([
            'valid' => true,
            'message' => 'Kode referral valid! Anda mendapatkan diskon 10%.',
            'base_amount' => $baseAmount,
            'final_amount' => (int) round($discountedAmount),
        ]);
    }

    private function assertPaymentBelongsToCourse(Course $course, ManualPayment $manualPayment): void
    {
        if ((int) $manualPayment->course_id !== (int) $course->id) {
            abort(404);
        }
    }
}
