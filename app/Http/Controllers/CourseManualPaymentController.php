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
