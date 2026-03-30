<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ManualPayment;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoursePaymentController extends Controller
{
    /**
     * Purchase/enroll status for current user.
     */
    public function status(Request $request, Course $course)
    {
        $user = $request->user();

        $enrollment = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->latest('id')
            ->first();

        $latestPayment = ManualPayment::query()
            ->with('proofs')
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->latest('id')
            ->first();

        $isFree = (int) ($course->price ?? 0) <= 0;

        return response()->json([
            'status' => 'success',
            'message' => 'Status pembelian course',
            'data' => [
                'course_id' => (int) $course->id,
                'is_free' => $isFree,
                'price' => (int) ($course->price ?? 0),
                'enrollment' => $enrollment,
                'latest_payment' => $latestPayment,
                'can_learn' => $this->canLearn($course, $enrollment, $latestPayment),
            ],
        ]);
    }

    /**
     * Enroll to course.
     * - If free: activate immediately + create finance trace (ManualPayment settled amount 0)
     * - If paid: create/update enrollment pending + create/update ManualPayment pending (no proof yet)
     */
    public function enroll(Request $request, Course $course)
    {
        $user = $request->user();

        $isFree = (int) ($course->price ?? 0) <= 0;

        if ($isFree) {
            $enrollment = Enrollment::updateOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                ['status' => 'active']
            );

            ManualPayment::create([
                'course_id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'user_id' => $user->id,
                'order_id' => 'FREE-CRS-' . strtoupper(uniqid()),
                'amount' => 0,
                'currency' => 'IDR',
                'method' => 'free',
                'status' => 'settled',
                'metadata' => ['source' => 'course', 'type' => 'free'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Enroll course gratis berhasil',
                'data' => [
                    'enrollment' => $enrollment->fresh(),
                ],
            ], 201);
        }

        $validated = $request->validate([
            'whatsapp' => 'nullable|string|max:32',
            'referral_code' => 'nullable|string|max:64',
        ]);

        $whatsapp = isset($validated['whatsapp']) ? trim((string) $validated['whatsapp']) : null;
        $referralCode = isset($validated['referral_code']) ? trim((string) $validated['referral_code']) : null;

        DB::beginTransaction();
        try {
            $enrollment = Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                ['status' => 'pending']
            );

            if ($enrollment->status !== 'pending') {
                $enrollment->status = 'pending';
                $enrollment->save();
            }

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
            $manualPayment->rejection_reason = null;
            $manualPayment->whatsapp_number = $whatsapp;
            $manualPayment->referral_code = $referralCode;
            if (!$manualPayment->order_id) {
                $manualPayment->order_id = 'MP-' . strtoupper(uniqid());
            }
            $manualPayment->metadata = array_merge((array) ($manualPayment->metadata ?? []), [
                'source' => 'course',
            ]);
            $manualPayment->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pembelian dibuat. Silakan upload bukti pembayaran.',
                'data' => [
                    'enrollment' => $enrollment,
                    'manual_payment' => $manualPayment,
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat pembelian: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload course payment proof (QRIS).
     * Expects: payment_proof image.
     */
    public function uploadProof(Request $request, Course $course)
    {
        $user = $request->user();

        $validated = $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'whatsapp' => 'nullable|string|max:32',
            'referral_code' => 'nullable|string|max:64',
        ]);

        $isFree = (int) ($course->price ?? 0) <= 0;
        if ($isFree) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course ini gratis, tidak perlu upload bukti bayar.',
            ], 400);
        }

        $whatsapp = isset($validated['whatsapp']) ? trim((string) $validated['whatsapp']) : null;
        $referralCode = isset($validated['referral_code']) ? trim((string) $validated['referral_code']) : null;

        DB::beginTransaction();
        try {
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
            $manualPayment->rejection_reason = null;
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

            // Keep enrollment pending until admin approval
            if ($enrollment->status !== 'pending') {
                $enrollment->status = 'pending';
                $enrollment->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.',
                'data' => [
                    'enrollment' => $enrollment,
                    'manual_payment' => $manualPayment->load('proofs'),
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal upload bukti bayar: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function canLearn(Course $course, ?Enrollment $enrollment, ?ManualPayment $latestPayment): bool
    {
        $enrolledActive = $enrollment && $enrollment->status === 'active';
        $settled = $latestPayment && $latestPayment->status === 'settled';

        // For free courses, allow only if active enrollment exists OR has settled trace
        // (consistent with web learn gate).
        return $enrolledActive || $settled;
    }
}
