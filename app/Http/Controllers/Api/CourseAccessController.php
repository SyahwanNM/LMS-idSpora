<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\ManualPayment;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseAccessController extends Controller
{
    /**
     * List modules user can access for a course.
     */
    public function modules(Request $request, Course $course)
    {
        $user = $request->user();

        $deny = $this->denyReasonIfCannotLearn($user->id, $course);
        if ($deny) {
            return response()->json([
                'status' => 'error',
                'message' => $deny,
            ], 403);
        }

        $modulesQuery = $course->modules()->orderBy('order_no');

        $isFreeCourse = (int) ($course->price ?? 0) <= 0;
        $freeAccessMode = $isFreeCourse ? (string) ($course->free_access_mode ?? 'limit_2') : 'all';
        if ($isFreeCourse && $freeAccessMode === 'limit_2') {
            $modulesQuery->limit(3); // unlock full first unit (pdf+video+quiz)
        }

        $modules = $modulesQuery->get();

        $data = $modules->map(function (CourseModule $m) {
            return $this->modulePayload($m, includeFileUrl: false);
        })->values();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar modul',
            'data' => $data,
        ]);
    }

    /**
     * Module detail with file URL.
     */
    public function module(Request $request, Course $course, CourseModule $module)
    {
        $user = $request->user();

        $deny = $this->denyReasonIfCannotLearn($user->id, $course);
        if ($deny) {
            return response()->json([
                'status' => 'error',
                'message' => $deny,
            ], 403);
        }

        if ((int) $module->course_id !== (int) $course->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Modul tidak ditemukan',
            ], 404);
        }

        // Free course restriction: only first 2 modules
        $isFreeCourse = (int) ($course->price ?? 0) <= 0;
        $freeAccessMode = $isFreeCourse ? (string) ($course->free_access_mode ?? 'limit_2') : 'all';
        if ($isFreeCourse && $freeAccessMode === 'limit_2') {
            $allowedIds = $course->modules()->orderBy('order_no')->limit(3)->pluck('id')->all();
            if (!in_array((int) $module->id, array_map('intval', $allowedIds), true)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Course gratis ini hanya membuka 2 modul pertama.',
                ], 403);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail modul',
            'data' => $this->modulePayload($module, includeFileUrl: true),
        ]);
    }

    /**
     * Mark module completed (video/pdf).
     */
    public function complete(Request $request, Course $course, CourseModule $module)
    {
        $user = $request->user();

        $enrollmentOrError = $this->resolveActiveEnrollmentOrError($user->id, $course);
        if (is_array($enrollmentOrError)) {
            return response()->json($enrollmentOrError['body'], $enrollmentOrError['status']);
        }
        $enrollment = $enrollmentOrError;

        if ((int) $module->course_id !== (int) $course->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Modul tidak ditemukan',
            ], 404);
        }

        $type = strtolower(trim((string) ($module->type ?? '')));
        if ($type === 'quiz') {
            return response()->json([
                'status' => 'error',
                'message' => 'Progress kuis ditentukan dari kelulusan kuis.',
            ], 422);
        }

        Progress::query()->updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'course_module_id' => $module->id,
            ],
            [
                'completed' => true,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Progress tersimpan',
        ]);
    }

    /**
     * Overall course progress percent.
     */
    public function progress(Request $request, Course $course)
    {
        $user = $request->user();

        $enrollmentOrError = $this->resolveActiveEnrollmentOrError($user->id, $course);
        if (is_array($enrollmentOrError)) {
            return response()->json($enrollmentOrError['body'], $enrollmentOrError['status']);
        }
        $enrollment = $enrollmentOrError;

        $course->loadMissing('modules');
        $enrollment->setRelation('course', $course);
        $enrollment->loadMissing('progress');

        return response()->json([
            'status' => 'success',
            'message' => 'Progress course',
            'data' => [
                'course_id' => (int) $course->id,
                'progress_percent' => $enrollment->getProgressPercentage(),
            ],
        ]);
    }

    private function denyReasonIfCannotLearn(int $userId, Course $course): ?string
    {
        // Active enrollment OR settled manual payment.
        $enrolledActive = Enrollment::query()
            ->where('user_id', $userId)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        $hasSettledPayment = ManualPayment::query()
            ->where('user_id', $userId)
            ->where('course_id', $course->id)
            ->where('status', 'settled')
            ->exists();

        if (!$enrolledActive && !$hasSettledPayment) {
            return 'Silakan lakukan pembelian/enroll course terlebih dahulu.';
        }

        return null;
    }

    private function resolveActiveEnrollmentOrError(int $userId, Course $course): Enrollment|array
    {
        $enrollment = Enrollment::query()
            ->where('user_id', $userId)
            ->where('course_id', $course->id)
            ->first();

        $hasSettledPayment = ManualPayment::query()
            ->where('user_id', $userId)
            ->where('course_id', $course->id)
            ->where('status', 'settled')
            ->exists();

        if (!$enrollment) {
            if (!$hasSettledPayment) {
                return [
                    'status' => 403,
                    'body' => [
                        'status' => 'error',
                        'message' => 'Silakan lakukan pembelian/enroll course terlebih dahulu.',
                    ],
                ];
            }
            $enrollment = Enrollment::create([
                'user_id' => $userId,
                'course_id' => $course->id,
                'status' => 'active',
            ]);
        }

        if ($enrollment->status !== 'active' && $hasSettledPayment) {
            $enrollment->status = 'active';
            $enrollment->save();
        }

        if ($enrollment->status !== 'active') {
            return [
                'status' => 403,
                'body' => [
                    'status' => 'error',
                    'message' => 'Pembayaran belum disetujui admin.',
                ],
            ];
        }

        return $enrollment;
    }

    private function modulePayload(CourseModule $m, bool $includeFileUrl): array
    {
        $contentUrl = (string) ($m->content_url ?? '');

        $fileUrl = null;
        if ($includeFileUrl && $contentUrl !== '') {
            // For quiz, content_url can be a placeholder. Only generate URL if path exists.
            $fileUrl = Storage::disk('public')->url($contentUrl);
        }

        return [
            'id' => (int) $m->id,
            'course_id' => (int) $m->course_id,
            'order_no' => (int) ($m->order_no ?? 0),
            'title' => (string) ($m->title ?? ''),
            'description' => $m->description,
            'type' => (string) ($m->type ?? ''),
            'is_free' => (bool) ($m->is_free ?? false),
            'preview_pages' => (int) ($m->preview_pages ?? 0),
            'duration' => (int) ($m->duration ?? 0),
            'content_path' => $contentUrl !== '' ? $contentUrl : null,
            'content_url' => $includeFileUrl ? $fileUrl : null,
            'file_name' => $m->file_name,
            'mime_type' => $m->mime_type,
            'file_size' => (int) ($m->file_size ?? 0),
        ];
    }
}
