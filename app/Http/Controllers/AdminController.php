<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Event;
use App\Models\Certificate;
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
        // Get statistics data
        $activeUsers = User::where('role', 'user')->count();
        $totalCourses = Course::count();
        $totalEvents = Event::count();
        $totalCertificates = Certificate::count();
        
        // Calculate total revenue from courses
        $totalRevenue = Course::sum('price') ?? 0;
        
        // Get recent activities (you can customize this based on your needs)
        $recentActivities = [
            [
                'user' => 'John Doe',
                'action' => 'Enrolled in Web Development Course',
                'time' => '2 hours ago',
                'avatar' => 'https://ui-avatars.com/api/?name=John+Doe&background=3b82f6&color=fff'
            ],
            [
                'user' => 'Sarah Smith',
                'action' => 'Completed JavaScript Fundamentals',
                'time' => '4 hours ago',
                'avatar' => 'https://ui-avatars.com/api/?name=Sarah+Smith&background=10b981&color=fff'
            ],
            [
                'user' => 'Mike Johnson',
                'action' => 'Registered for Tech Conference 2024',
                'time' => '6 hours ago',
                'avatar' => 'https://ui-avatars.com/api/?name=Mike+Johnson&background=f59e0b&color=fff'
            ]
        ];

        return view('admin.dashboard', compact(
            'activeUsers',
            'totalCourses', 
            'totalEvents',
            'totalCertificates',
            'totalRevenue',
            'recentActivities'
        ));
    }

    public function activeUsersCount()
    {
        $count = User::where('role', 'user')->count();
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