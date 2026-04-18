<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ManualPayment;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoursePaymentController extends Controller
{
    private const REJECTION_REASONS = [
        'Nominal pembayaran kurang',
        'Nominal pembayaran lebih',
        'Gambar bukti pembayaran blur/buram. Silahkan kirim ulang',
        'Pembayaran dinyatakan tidak valid',
    ];

    /**
     * List course payments (admin).
     * Optional query: status, course_id, user_id.
     */
    public function index(Request $request)
    {
        $perPage = max(1, min((int) $request->query('per_page', 10), 100));

        $query = ManualPayment::query()
            ->with(['user:id,name,email', 'course:id,name,price', 'proofs'])
            ->whereNotNull('course_id')
            ->orderByDesc('id');

        $status = trim((string) $request->query('status', ''));
        if ($status !== '') {
            $query->where('status', $status);
        }

        $courseId = $request->query('course_id');
        if (is_numeric($courseId)) {
            $query->where('course_id', (int) $courseId);
        }

        $userId = $request->query('user_id');
        if (is_numeric($userId)) {
            $query->where('user_id', (int) $userId);
        }

        $payments = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar pembayaran course',
            'data' => $payments,
        ]);
    }

    public function show(ManualPayment $coursePayment)
    {
        if (!$coursePayment->course_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pembayaran course tidak ditemukan',
            ], 404);
        }

        $coursePayment->load(['user', 'course', 'proofs', 'enrollment']);

        return response()->json([
            'status' => 'success',
            'message' => 'Detail pembayaran course',
            'data' => $coursePayment,
        ]);
    }

    public function approve(Request $request, ManualPayment $coursePayment)
    {
        if (!$coursePayment->course_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pembayaran course tidak ditemukan',
            ], 404);
        }

        $course = Course::find($coursePayment->course_id);
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course tidak ditemukan',
            ], 404);
        }

        DB::beginTransaction();
        try {
            if ($coursePayment->status !== 'settled') {
                $coursePayment->status = 'settled';
                $coursePayment->rejection_reason = null;
                $coursePayment->save();

                $this->processReferralCommission($course, $coursePayment);
            }

            $enrollment = $coursePayment->enrollment;
            if (!$enrollment && $coursePayment->enrollment_id) {
                $enrollment = Enrollment::find($coursePayment->enrollment_id);
            }

            if (!$enrollment) {
                $enrollment = Enrollment::firstOrCreate(
                    ['user_id' => $coursePayment->user_id, 'course_id' => $course->id],
                    ['status' => 'active']
                );
            }

            if ($enrollment->status !== 'active') {
                $enrollment->status = 'active';
                $enrollment->save();
            }

            if ((int) ($coursePayment->enrollment_id ?? 0) !== (int) $enrollment->id) {
                $coursePayment->enrollment_id = $enrollment->id;
                $coursePayment->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran course berhasil di-approve',
                'data' => $coursePayment->fresh()->load(['user', 'course', 'proofs', 'enrollment']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal approve pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reject(Request $request, ManualPayment $coursePayment)
    {
        if (!$coursePayment->course_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pembayaran course tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'in:' . implode(',', self::REJECTION_REASONS)],
        ]);

        DB::beginTransaction();
        try {
            $coursePayment->status = 'rejected';
            $coursePayment->rejection_reason = trim((string) $validated['reason']);
            $coursePayment->save();

            if ($coursePayment->enrollment) {
                $coursePayment->enrollment->status = 'canceled';
                $coursePayment->enrollment->save();
            } elseif ($coursePayment->enrollment_id) {
                $enr = Enrollment::find($coursePayment->enrollment_id);
                if ($enr) {
                    $enr->status = 'canceled';
                    $enr->save();
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran course berhasil di-reject',
                'data' => $coursePayment->fresh()->load(['user', 'course', 'proofs', 'enrollment']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal reject pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function pending(Request $request, ManualPayment $coursePayment)
    {
        if (!$coursePayment->course_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pembayaran course tidak ditemukan',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $coursePayment->status = 'pending';
            $coursePayment->rejection_reason = null;
            $coursePayment->save();

            if ($coursePayment->enrollment) {
                $coursePayment->enrollment->status = 'pending';
                $coursePayment->enrollment->save();
            } elseif ($coursePayment->enrollment_id) {
                $enr = Enrollment::find($coursePayment->enrollment_id);
                if ($enr) {
                    $enr->status = 'pending';
                    $enr->save();
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Status pembayaran course diubah ke pending',
                'data' => $coursePayment->fresh()->load(['user', 'course', 'proofs', 'enrollment']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal ubah status: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function processReferralCommission(Course $course, ManualPayment $coursePayment): void
    {
        if (!(bool) ($course->is_reseller_course ?? false)) {
            return;
        }

        if (empty($coursePayment->referral_code)) {
            return;
        }

        $referrer = User::query()->where('referral_code', $coursePayment->referral_code)->first();
        if (!$referrer || (int) $referrer->id === (int) $coursePayment->user_id) {
            return;
        }

        $commissionAmount = ((float) $coursePayment->amount) * 0.10;
        if ($commissionAmount <= 0) {
            return;
        }

        $description = 'Komisi Course: ' . (string) ($course->name ?? '');

        $existingReferral = Referral::query()
            ->where('user_id', $referrer->id)
            ->where('referred_user_id', $coursePayment->user_id)
            ->where('description', $description)
            ->first();

        if ($existingReferral) {
            return;
        }

        Referral::create([
            'user_id' => $referrer->id,
            'referred_user_id' => $coursePayment->user_id,
            'amount' => $commissionAmount,
            'status' => 'paid',
            'description' => $description,
        ]);

        $referrer->increment('wallet_balance', $commissionAmount);
    }
}
