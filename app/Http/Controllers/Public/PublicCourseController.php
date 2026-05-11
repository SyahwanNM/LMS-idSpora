<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

use App\Models\Course;
use App\Models\Carousel;
use App\Models\Enrollment;
use App\Models\ManualPayment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicCourseController extends Controller
{
    public function index(Request $request)
    {
        // Only show published courses to users
        $query = Course::query()
            ->with(['category','modules'])
            ->withCount([
                'enrollments as enrollments_count' => function ($q) {
                    $q->select(DB::raw('COUNT(DISTINCT user_id)'))
                        ->whereIn('status', ['active', 'completed']);
                },
            ])
            ->withAvg('reviews as rating_avg', 'rating')
            ->where('status','active');

        if($search = $request->get('search')){
            $query->where(function($q) use ($search){
                $q->where('name','like',"%$search%")
                  ->orWhere('description','like',"%$search%")
                  ->orWhereHas('category', function($c) use ($search){
                      $c->where('name','like',"%$search%");
                  });
            });
        }

        if ($level = $request->get('level')) {
            $query->where('level', $level);
        }

        if ($category = $request->get('category')) {
            $query->where('category_id', $category);
        }

        if ($topic = $request->get('topic')) {
            $query->where('name', 'like', "%$topic%");
        }

        if ($request->boolean('free')) {
            $query->where('price', 0);
        }

        if ($sort = $request->get('price')) {
            if (in_array($sort, ['asc', 'desc'])) {
                // Sort by effective price (after discount if active)
                $query->orderByRaw("
                    CASE
                        WHEN discount_percent > 0
                             AND (discount_start IS NULL OR discount_start <= NOW())
                             AND (discount_end IS NULL OR discount_end >= NOW())
                        THEN price * (1 - discount_percent / 100)
                        ELSE price
                    END " . strtoupper($sort)
                );
            }
        } else {
            $query->latest();
        }

        $courses = $query->paginate(12)->withQueryString();
        // Featured: published-only, most recently updated
        $featuredCourses = Course::with(['category','modules'])
            ->withCount([
                'enrollments as enrollments_count' => function ($q) {
                    $q->select(DB::raw('COUNT(DISTINCT user_id)'))
                        ->where('status', 'active');
                },
            ])
            ->where('status','active')
            ->orderBy('updated_at','desc')
            ->take(8)
            ->get();

        // Data for filter dropdowns
        $categories = \App\Models\Category::orderBy('name')->get();
        $topics = Course::where('status', 'active')->distinct()->orderBy('name')->pluck('name');

        // Get carousel images for course page
        $courseCarousels = Carousel::active()
            ->forLocation('course')
            ->orderBy('order')
            ->get();

        $learnableCourseIds = [];
        $continueEnrollments = collect();
        $user = $request->user();
        if ($user) {
            $userSavedCourseIds = DB::table('user_saved_courses')
                ->where('user_id', $user->id)
                ->pluck('course_id')->toArray();

            $courses->getCollection()->transform(function ($c) use ($userSavedCourseIds) {
                $c->is_saved = in_array($c->id, $userSavedCourseIds);
                return $c;
            });

            $fromEnrollments = Enrollment::query()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->pluck('course_id')
                ->all();

            $fromManualPayments = ManualPayment::query()
                ->where('user_id', $user->id)
                ->whereNotNull('course_id')
                ->where('status', 'settled')
                ->pluck('course_id')
                ->all();

            $learnableCourseIds = array_values(array_unique(array_merge($fromEnrollments, $fromManualPayments)));

            // Continue learning: active enrollments, sorted by recent activity
            $continueEnrollments = Enrollment::query()
                ->with([
                    'course' => function ($q) {
                        $q->with(['category', 'modules'])
                            ->withCount([
                                'enrollments as enrollments_count' => function ($qq) {
                                    $qq->select(DB::raw('COUNT(DISTINCT user_id)'))
                                        ->whereIn('status', ['active', 'completed']);
                                },
                            ]);
                    },
                    'progress' => function ($q) {
                        $q->where('completed', true);
                    },
                ])
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->orderByDesc('updated_at')
                ->take(8)
                ->get()
                ->filter(function ($enrollment) {
                    return $enrollment->getProgressPercentage() < 100;
                })
                ->values();
        }

        return view('course.index', compact('courses','featuredCourses', 'courseCarousels', 'learnableCourseIds', 'continueEnrollments', 'categories', 'topics'));
    }
}
