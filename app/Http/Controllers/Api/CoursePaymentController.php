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

        abort(403, 'Pembayaran manual dinonaktifkan. Gunakan Midtrans.');
    }

    /**
     * Upload course payment proof (QRIS).
     * Expects: payment_proof image.
     */
    public function uploadProof(Request $request, Course $course)
    {
        abort(403, 'Pembayaran manual dinonaktifkan. Gunakan Midtrans.');
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
