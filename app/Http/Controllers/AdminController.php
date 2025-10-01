<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Event;
use App\Models\Certificate;
use App\Models\DashboardMetric;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        // Middleware akan dihandle di level route
    }

    public function dashboard()
    {
        // Current totals
        $activeUsers = User::count(); // total accounts
        $totalCourses = Course::count();
        $totalEvents = Event::count();
        $totalCertificates = Certificate::count();
        $totalRevenue = Course::sum('price') ?? 0; // Adjust if you have real transaction table

        // Snapshot logic (daily)
        $today = now()->startOfDay()->toDateString();
        $yesterdayDate = now()->subDay()->startOfDay()->toDateString();

        // Ensure today's snapshot exists (idempotent)
        DashboardMetric::firstOrCreate(
            ['snapshot_date' => $today],
            [
                'users_count' => $activeUsers,
                'courses_count' => $totalCourses,
                'events_count' => $totalEvents,
                'revenue_total' => $totalRevenue,
            ]
        );

    $todaySnapshot = DashboardMetric::where('snapshot_date', $today)->first();
    $yesterdaySnapshot = DashboardMetric::where('snapshot_date', $yesterdayDate)->first();

        // Helper closure to compute percent change
        $percentChange = function ($current, $previous) {
            if ($previous === null) return null; // No data
            if ($previous == 0) return $current > 0 ? 100 : 0; // Avoid division by zero
            return round((($current - $previous) / $previous) * 100, 1);
        };

        // Determine baseline (yesterday if exists, otherwise today's first snapshot for intra-day change)
        $usingIntraDayBaseline = false;
        if (!$yesterdaySnapshot) {
            $usingIntraDayBaseline = true;
        }

        $baselineUsers = $yesterdaySnapshot->users_count ?? ($todaySnapshot->users_count ?? null);
        $baselineCourses = $yesterdaySnapshot->courses_count ?? ($todaySnapshot->courses_count ?? null);
        $baselineEvents = $yesterdaySnapshot->events_count ?? ($todaySnapshot->events_count ?? null);
        $baselineRevenue = $yesterdaySnapshot->revenue_total ?? ($todaySnapshot->revenue_total ?? null);

        $activeUsersChangePercent = $percentChange($activeUsers, $baselineUsers);
        $totalCoursesChangePercent = $percentChange($totalCourses, $baselineCourses);
        $totalEventsChangePercent = $percentChange($totalEvents, $baselineEvents);
        $totalRevenueChangePercent = $percentChange($totalRevenue, $baselineRevenue);

        // Recent activities placeholder (replace with real audit/activity log later)
        $recentActivities = [
            [
                'user' => 'System',
                'action' => 'Dashboard metrics updated',
                'time' => 'Just now',
                'avatar' => 'https://ui-avatars.com/api/?name=System&background=6b7280&color=fff'
            ]
        ];

        return view('admin.dashboard', compact(
            'activeUsers',
            'totalCourses',
            'totalEvents',
            'totalCertificates',
            'totalRevenue',
            'recentActivities',
            'activeUsersChangePercent',
            'totalCoursesChangePercent',
            'totalEventsChangePercent',
            'totalRevenueChangePercent',
            'usingIntraDayBaseline'
        ));
    }

    public function activeUsersCount()
    {
    // Return total accounts count
    $count = User::count();
        return response()->json(['count' => $count]);
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|integer|min:0',
            'duration' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle image upload
        $imagePath = $request->file('image')->store('courses', 'public');

        // Create course
        Course::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'level' => $request->level,
            'price' => $request->price,
            'duration' => $request->duration,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Course created successfully!');
    }

    public function storeEvent(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'speaker' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle image upload
        $imagePath = $request->file('image')->store('events', 'public');

        // Create event
        Event::create([
            'title' => $request->title,
            'speaker' => $request->speaker,
            'description' => $request->description,
            'location' => $request->location,
            'price' => $request->price,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Event created successfully!');
    }

    public function reports()
    {
        // Handle reports page
        return view('admin.reports');
    }
}