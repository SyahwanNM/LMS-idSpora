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

        $level = strtolower(trim((string) $request->query('level', '')));
        if (in_array($level, ['beginner', 'intermediate', 'advanced'], true)) {
            $query->where('level', $level);
        }

        $category = trim((string) $request->query('category', ''));
        if ($category !== '') {
            $query->whereHas('category', fn($q) => $q->where('id', $category)
                ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($category)]));
        }

        $topic = trim((string) $request->query('topic', ''));
        if ($topic !== '') {
            $query->where('name', 'like', "%{$topic}%");
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

        $courses = $query->paginate($perPage)->appends($request->query());

        return response()->json([
            'status' => 'success',
            'message' => 'List course',
            'data' => CourseResource::collection($courses),
            'pagination' => [
                'current_page' => $courses->currentPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
                'last_page' => $courses->lastPage(),
            ],
        ]);
    }

    /**
     * Public course detail (active courses only).
     */
    public function show(Request $request, Course $course)
    {
        if (((string) ($course->status ?? '')) !== 'active') {
            return response()->json(['status' => 'error', 'message' => 'Course tidak ditemukan'], 404);
        }

        $course->load(['category', 'modules' => fn($q) => $q->orderBy('order_no')])
            ->loadCount(['modules', 'enrollments as enrollments_count'])
            ->loadAvg('reviews', 'rating');

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail course',
            'data'    => new CourseResource($course),
        ]);
    }

    /**
     * Course reviews/ratings.
     */
    public function reviews(Request $request, Course $course)
    {
        if (((string) ($course->status ?? '')) !== 'active') {
            return response()->json(['status' => 'error', 'message' => 'Course tidak ditemukan'], 404);
        }

        $perPage = max(1, min((int) $request->query('per_page', 10), 50));

        $reviews = \App\Models\Review::with('user:id,name,avatar')
            ->where('course_id', $course->id)
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'status'  => 'success',
            'message' => 'Ulasan course',
            'data'    => $reviews->map(fn($r) => [
                'id'         => $r->id,
                'rating'     => (int) $r->rating,
                'comment'    => $r->comment,
                'user'       => $r->user ? ['id' => $r->user->id, 'name' => $r->user->name] : null,
                'created_at' => $r->created_at?->toISOString(),
            ]),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'per_page'     => $reviews->perPage(),
                'total'        => $reviews->total(),
                'last_page'    => $reviews->lastPage(),
            ],
        ]);
    }

    /**
     * Submit review for an enrolled course.
     */
    public function submitReview(Request $request, Course $course)
    {
        if (((string) ($course->status ?? '')) !== 'active') {
            return response()->json(['status' => 'error', 'message' => 'Course tidak ditemukan'], 404);
        }

        $user = $request->user();

        $canLearn = \App\Models\Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->exists();

        if (!$canLearn) {
            return response()->json(['status' => 'error', 'message' => 'Anda belum mengikuti course ini'], 403);
        }

        $existing = \App\Models\Review::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return response()->json(['status' => 'error', 'message' => 'Anda sudah memberikan ulasan'], 409);
        }

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = \App\Models\Review::create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'rating'    => $validated['rating'],
            'comment'   => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Ulasan berhasil dikirim',
            'data'    => ['id' => $review->id, 'rating' => $review->rating, 'comment' => $review->comment],
        ], 201);
    }
}
