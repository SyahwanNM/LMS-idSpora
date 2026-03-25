<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Public list of active courses.
     */
    public function index(Request $request)
    {
        $perPage = max(1, min((int) $request->query('per_page', 12), 100));

        $query = Course::query()
            ->with(['category'])
            ->withCount('modules')
            ->where('status', 'active');

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($cat) use ($search) {
                        $cat->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $level = trim((string) $request->query('level', ''));
        if ($level !== '') {
            $query->where('level', $level);
        }

        if ($request->has('free')) {
            $isFree = filter_var($request->query('free'), FILTER_VALIDATE_BOOL);
            if ($isFree) {
                $query->where('price', 0);
            }
        }

        $priceSort = (string) $request->query('price', '');
        if (in_array($priceSort, ['asc', 'desc'], true)) {
            $query->orderBy('price', $priceSort);
        } else {
            $query->latest();
        }

        $courses = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'List course',
            'data' => CourseResource::collection($courses),
        ]);
    }

    /**
     * Public course detail (active courses only).
     */
    public function show(Request $request, Course $course)
    {
        if (((string) ($course->status ?? '')) !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Course tidak ditemukan',
            ], 404);
        }

        $course->load([
            'category',
            'modules' => function ($q) {
                $q->orderBy('order_no');
            },
        ])->loadCount('modules');

        return response()->json([
            'status' => 'success',
            'message' => 'Detail course',
            'data' => new CourseResource($course),
        ]);
    }
}
