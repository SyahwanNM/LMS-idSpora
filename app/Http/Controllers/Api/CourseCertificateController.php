<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRM\CertificateController;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseCertificateController extends Controller
{
    /**
     * GET /api/courses/{course}/certificate
     *
     * Cek status sertifikat course milik user yang login.
     * Sertifikat tersedia jika enrollment status = completed
     * atau progress >= 100%.
     */
    public function show(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();

        $enrollment = Enrollment::with('user')
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda belum terdaftar di course ini.',
            ], 403);
        }

        $progressPercent  = $enrollment->getProgressPercentage();
        $certificateReady = $enrollment->status === 'completed'
            || !is_null($enrollment->completed_at)
            || $progressPercent >= 100;

        if (!$certificateReady) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sertifikat belum tersedia. Selesaikan semua modul terlebih dahulu.',
                'data'    => [
                    'progress_percent' => (int) $progressPercent,
                    'status'           => $enrollment->status,
                ],
            ], 422);
        }

        // Auto-generate certificate number & tandai completed
        if ($enrollment->status !== 'completed') {
            $enrollment->status = 'completed';
        }
        if (!$enrollment->completed_at) {
            $enrollment->completed_at = now();
        }
        if (!$enrollment->certificate_issued_at) {
            $enrollment->certificate_issued_at = now();
        }
        if (empty($enrollment->certificate_number)) {
            $enrollment->certificate_number = CertificateController::generateCertificateNumberCourse($course, $enrollment);
        }
        $enrollment->save();
        $enrollment->refresh();

        return response()->json([
            'status'  => 'success',
            'message' => 'Sertifikat tersedia.',
            'data'    => $this->formatCertificate($course, $enrollment),
        ]);
    }

    /**
     * GET /api/me/course-certificates
     *
     * Daftar semua sertifikat course milik user yang login.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $enrollments = Enrollment::with('course:id,name,level')
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereNotNull('certificate_number')
            ->latest('certificate_issued_at')
            ->get();

        $data = $enrollments->map(fn($e) => [
            'enrollment_id'         => $e->id,
            'course_id'             => $e->course_id,
            'course_name'           => $e->course?->name,
            'course_level'          => $e->course?->level,
            'certificate_number'    => $e->certificate_number,
            'certificate_issued_at' => $e->certificate_issued_at?->toISOString(),
            'completed_at'          => $e->completed_at?->toISOString(),
            'download_url'          => route('api.courses.certificate.download', [
                'course'     => $e->course_id,
                'enrollment' => $e->id,
            ]),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar sertifikat course.',
            'data'    => $data,
        ]);
    }

    /**
     * GET /api/courses/{course}/certificate/download
     *
     * Download sertifikat course dalam format PDF.
     */
    public function download(Request $request, Course $course)
    {
        $user = $request->user();

        $enrollment = Enrollment::with('user')
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda belum terdaftar di course ini.',
            ], 403);
        }

        $progressPercent  = $enrollment->getProgressPercentage();
        $certificateReady = $enrollment->status === 'completed'
            || !is_null($enrollment->completed_at)
            || $progressPercent >= 100;

        if (!$certificateReady) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sertifikat belum tersedia. Selesaikan semua modul terlebih dahulu.',
                'data'    => ['progress_percent' => (int) $progressPercent],
            ], 422);
        }

        // Pastikan certificate_number sudah ada
        if (empty($enrollment->certificate_number)) {
            $enrollment->update([
                'certificate_number'    => CertificateController::generateCertificateNumberCourse($course, $enrollment),
                'certificate_issued_at' => now(),
                'status'                => 'completed',
                'completed_at'          => $enrollment->completed_at ?? now(),
            ]);
            $enrollment->refresh();
        }

        // Delegate ke CRM CertificateController yang sudah ada
        return app(CertificateController::class)->downloadCourse($request, $course, $enrollment->id);
    }

    // -------------------------------------------------------------------------

    private function formatCertificate(Course $course, Enrollment $enrollment): array
    {
        return [
            'enrollment_id'         => $enrollment->id,
            'course_id'             => $course->id,
            'course_name'           => $course->name,
            'course_level'          => $course->level,
            'recipient_name'        => $enrollment->user?->name,
            'certificate_number'    => $enrollment->certificate_number,
            'certificate_issued_at' => $enrollment->certificate_issued_at?->toISOString(),
            'completed_at'          => $enrollment->completed_at?->toISOString(),
            'progress_percent'      => (int) $enrollment->getProgressPercentage(),
            'download_url'          => route('api.courses.certificate.download', [
                'course'     => $course->id,
                'enrollment' => $enrollment->id,
            ]),
        ];
    }
}
