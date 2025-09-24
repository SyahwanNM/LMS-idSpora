<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;

class LandingPageController extends Controller
{
    public function index()
    {
        // Get upcoming events (next 4 events)
        $upcomingEvents = Event::where('event_date', '>=', Carbon::now()->format('Y-m-d'))
            ->orderBy('event_date', 'asc')
            ->limit(4)
            ->get();

        return view('landing-page', compact('upcomingEvents'));
    }
}

