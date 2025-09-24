<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Event;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        // Get 4 latest courses or best rated courses
        $featuredCourses = Course::with(['category', 'modules'])
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        // Get 4 latest events
        $featuredEvents = Event::orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('landing-page', compact('featuredCourses', 'featuredEvents'));
    }
}