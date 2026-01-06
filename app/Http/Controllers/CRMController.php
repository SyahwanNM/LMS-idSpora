<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Enrollment;
use App\Models\Feedback;
use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CRMController extends Controller
{
    /**
     * Display CRM Dashboard
     */
    public function dashboard()
    {
        // Only admin can access
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        // Get statistics
        $totalCustomers = User::where('role', '!=', 'admin')->count();
        $activeCustomers = User::where('role', '!=', 'admin')
            ->where(function($query) {
                $query->whereHas('eventRegistrations', function($q) {
                    $q->where('status', 'active');
                })
                ->orWhereHas('enrollments', function($q) {
                    $q->where('status', 'active');
                });
            })
            ->count();
        
        $totalRegistrations = EventRegistration::where('status', 'active')->count();
        $totalEnrollments = Enrollment::where('status', 'active')->count();
        
        // Recent registrations (only show registrations with valid events)
        $recentRegistrations = EventRegistration::with(['user', 'event'])
            ->whereHas('event') // Only get registrations with valid events
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top events by registration
        $topEvents = Event::withCount(['registrations' => function($query) {
            $query->where('status', 'active');
        }])
        ->orderBy('registrations_count', 'desc')
        ->limit(5)
        ->get();

        return view('admin.crm.dashboard', compact(
            'totalCustomers',
            'activeCustomers',
            'totalRegistrations',
            'totalEnrollments',
            'recentRegistrations',
            'topEvents'
        ));
    }

    /**
     * Display list of customers
     */
    public function customers(Request $request)
    {
        // Only admin can access
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        $query = User::where('role', '!=', 'admin')
            ->withCount(['eventRegistrations', 'enrollments']);

        // Search functionality
        if($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.crm.customers.index', compact('customers'));
    }

    /**
     * Show customer detail
     */
    public function showCustomer(User $customer)
    {
        // Only admin can access
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        // Prevent viewing admin as customer
        if($customer->role === 'admin'){
            abort(404);
        }

        // Load customer data with relationships
        $customer->load([
            'eventRegistrations.event',
            'enrollments.course'
        ]);

        $registrations = $customer->eventRegistrations()
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();

        $enrollments = $customer->enrollments()
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.crm.customers.show', compact('customer', 'registrations', 'enrollments'));
    }

    /**
     * Edit customer
     */
    public function editCustomer(User $customer)
    {
        // Only admin can access
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        // Prevent editing admin as customer
        if($customer->role === 'admin'){
            abort(404);
        }

        return view('admin.crm.customers.edit', compact('customer'));
    }

    /**
     * Update customer
     */
    public function updateCustomer(Request $request, User $customer)
    {
        // Only admin can access
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        // Prevent editing admin as customer
        if($customer->role === 'admin'){
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'bio' => 'nullable|string|max:1000',
            'role' => 'required|in:user,reseller,trainer',
        ]);

        $customer->update($request->only(['name', 'email', 'phone', 'website', 'bio', 'role']));

        return redirect()->route('admin.crm.customers.show', $customer)
            ->with('success', 'Data customer berhasil diperbarui');
    }

    /**
     * Display Feedback Analysis for Events and Courses
     */
    public function feedbackAnalysis(Request $request)
    {
        // Only admin can access
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        // Get filter parameters
        $type = $request->get('type', 'event'); // 'event' or 'course'
        $eventId = $request->get('event_id');
        $courseId = $request->get('course_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Base query for feedback statistics
        $feedbackQuery = Feedback::query();
        
        // Apply date filters if provided
        if($dateFrom) {
            $feedbackQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if($dateTo) {
            $feedbackQuery->whereDate('created_at', '<=', $dateTo);
        }

        // Base query for events with feedback
        $eventsQuery = Event::withCount(['registrations', 'feedbacks'])
            ->whereHas('feedbacks', function($q) use ($dateFrom, $dateTo) {
                if($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                }
                if($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                }
            });

        // Filter by event if selected
        if($eventId) {
            $eventsQuery->where('id', $eventId);
        }

        $events = $eventsQuery->orderBy('event_date', 'desc')->paginate(15);

        // Overall statistics (with date filter if applied)
        $totalFeedback = (clone $feedbackQuery)->count();
        $avgRating = (clone $feedbackQuery)->avg('rating');
        
        // Calculate speaker rating only from feedbacks that have this value
        $speakerFeedbackCount = (clone $feedbackQuery)->whereNotNull('speaker_rating')->count();
        $avgSpeakerRating = $speakerFeedbackCount > 0 ? (clone $feedbackQuery)->whereNotNull('speaker_rating')->avg('speaker_rating') : null;

        // Rating distribution (with date filter if applied)
        $ratingDistribution = (clone $feedbackQuery)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();

        // Top rated events (with date filter if applied)
        $topRatedEventsQuery = Event::withCount('feedbacks')
            ->whereHas('feedbacks', function($q) use ($dateFrom, $dateTo) {
                if($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                }
                if($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                }
            })
            ->withAvg('feedbacks', 'rating')
            ->withAvg('feedbacks', 'speaker_rating')
            ->orderBy('feedbacks_avg_rating', 'desc')
            ->limit(10);
        $topRatedEvents = $topRatedEventsQuery->get();

        // Recent feedbacks (with date filter if applied)
        $recentFeedbacksQuery = Feedback::with(['user', 'event'])
            ->whereHas('event'); // Only get feedbacks where event still exists
        if($dateFrom) {
            $recentFeedbacksQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if($dateTo) {
            $recentFeedbacksQuery->whereDate('created_at', '<=', $dateTo);
        }
        $recentFeedbacks = $recentFeedbacksQuery->orderBy('created_at', 'desc')->limit(10)->get();

        // If specific event selected, get detailed analysis
        $eventAnalysis = null;
        if($eventId) {
            $event = Event::withCount('feedbacks')
                ->withAvg('feedbacks', 'rating')
                ->withAvg('feedbacks', 'speaker_rating')
                ->find($eventId);

            if($event) {
                $eventFeedbacks = Feedback::where('event_id', $eventId)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->get();

                $eventRatingDistribution = Feedback::where('event_id', $eventId)
                    ->select('rating', DB::raw('count(*) as count'))
                    ->groupBy('rating')
                    ->orderBy('rating', 'desc')
                    ->get();

                $eventAnalysis = [
                    'event' => $event,
                    'feedbacks' => $eventFeedbacks,
                    'ratingDistribution' => $eventRatingDistribution,
                ];
            }
        }

        // Get all events for filter dropdown
        $allEvents = Event::whereHas('feedbacks')
            ->orderBy('title')
            ->get();

        // Count feedbacks with speaker rating for display
        $speakerFeedbackCount = (clone $feedbackQuery)->whereNotNull('speaker_rating')->count();

        // ========== COURSE FEEDBACK ANALYSIS ==========
        // Base query for course reviews statistics
        $reviewQuery = Review::query();
        
        // Apply date filters if provided
        if($dateFrom) {
            $reviewQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if($dateTo) {
            $reviewQuery->whereDate('created_at', '<=', $dateTo);
        }

        // Base query for courses with reviews
        $coursesQuery = Course::withCount('reviews')
            ->whereHas('reviews', function($q) use ($dateFrom, $dateTo) {
                if($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                }
                if($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                }
            });

        // Filter by course if selected
        if($courseId) {
            $coursesQuery->where('id', $courseId);
        }

        $courses = $coursesQuery->orderBy('name')->paginate(15);

        // Overall course statistics (with date filter if applied)
        $totalReviews = (clone $reviewQuery)->count();
        $avgCourseRating = (clone $reviewQuery)->avg('rating');

        // Course rating distribution (with date filter if applied)
        $courseRatingDistribution = (clone $reviewQuery)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();

        // Top rated courses (with date filter if applied)
        $topRatedCoursesQuery = Course::withCount('reviews')
            ->whereHas('reviews', function($q) use ($dateFrom, $dateTo) {
                if($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                }
                if($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                }
            })
            ->withAvg('reviews', 'rating')
            ->orderBy('reviews_avg_rating', 'desc')
            ->limit(10);
        $topRatedCourses = $topRatedCoursesQuery->get();

        // Recent reviews (with date filter if applied)
        $recentReviewsQuery = Review::with(['user', 'course']);
        if($dateFrom) {
            $recentReviewsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if($dateTo) {
            $recentReviewsQuery->whereDate('created_at', '<=', $dateTo);
        }
        $recentReviews = $recentReviewsQuery->orderBy('created_at', 'desc')->limit(10)->get();

        // If specific course selected, get detailed analysis
        $courseAnalysis = null;
        if($courseId) {
            $course = Course::withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->find($courseId);

            if($course) {
                $courseReviews = Review::where('course_id', $courseId)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->get();

                $courseRatingDistributionDetail = Review::where('course_id', $courseId)
                    ->select('rating', DB::raw('count(*) as count'))
                    ->groupBy('rating')
                    ->orderBy('rating', 'desc')
                    ->get();

                $courseAnalysis = [
                    'course' => $course,
                    'reviews' => $courseReviews,
                    'ratingDistribution' => $courseRatingDistributionDetail,
                ];
            }
        }

        // Get all courses for filter dropdown
        $allCourses = Course::whereHas('reviews')
            ->orderBy('name')
            ->get();

        return view('admin.crm.feedback.index', compact(
            'type',
            'events',
            'totalFeedback',
            'avgRating',
            'avgSpeakerRating',
            'ratingDistribution',
            'topRatedEvents',
            'recentFeedbacks',
            'eventAnalysis',
            'allEvents',
            'eventId',
            'speakerFeedbackCount',
            // Course data
            'courses',
            'totalReviews',
            'avgCourseRating',
            'courseRatingDistribution',
            'topRatedCourses',
            'recentReviews',
            'courseAnalysis',
            'allCourses',
            'courseId'
        ));
    }
}

