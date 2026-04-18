<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ManualPayment;
use Illuminate\Http\Request;

class MyCourseController extends Controller
{
    /**
     * List courses the current user can learn.
     * Rule: enrollment active OR manual payment settled.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $activeCourseIds = Enrollment::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'completed'])
            ->pluck('course_id')
            ->all();

        $settledCourseIds = ManualPayment::query()
            ->where('user_id', $user->id)
            ->whereNotNull('course_id')
            ->where('status', 'settled')
            ->pluck('course_id')
            ->all();

        $courseIds = array_values(array_unique(array_merge($activeCourseIds, $settledCourseIds)));

        $courses = Course::query()
            ->with(['category'])
            ->withCount('modules')
            ->whereIn('id', $courseIds)
            ->orderByDesc('updated_at')
            ->get();

        // Progress per course (only for active enrollment if exists)
        $enrollments = Enrollment::query()
            ->with(['course:id'])
            ->with(['progress' => function ($q) {
                $q->where('completed', true);
            }])
            ->where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->get()
            ->keyBy('course_id');

        $rows = $courses->map(function (Course $course) use ($enrollments) {
            $enr = $enrollments->get($course->id);
            $progressPercent = 0;
            if ($enr) {
                $enr->setRelation('course', $course);
                $progressPercent = $enr->getProgressPercentage();
            }

            $resource = new CourseResource($course);
            $data = $resource->toArray(request());
            $data['is_enrolled']      = true;
            $data['progress_percent'] = (int) $progressPercent;

            return $data;
        })->values();

        return response()->json([
            'status' => 'success',
            'message' => 'My courses',
            'data' => $rows,
        ]);
    }
}
