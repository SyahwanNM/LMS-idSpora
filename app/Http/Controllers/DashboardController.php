<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Course;

class DashboardController extends Controller
{
    public function index()
    {
        // Redirect admin users to admin dashboard just in case route protection misses
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Upcoming events (nearest future first)
        $upcomingEvents = Event::query()
            ->whereDate('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->limit(8)
            ->get();

        // Featured courses sample (adjust logic as needed)
        $featuredCourses = Course::query()
            ->with(['category', 'modules'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        return view('dashboard', [
            'upcomingEvents' => $upcomingEvents,
            'featuredCourses' => $featuredCourses,
        ]);
    }
}
