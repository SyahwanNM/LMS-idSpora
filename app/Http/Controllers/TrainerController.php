<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class TrainerController extends Controller
{
    /**
     * Show trainer profile with their courses
     */
    public function show()
    {
        $trainer = Auth::user();

        // Get courses belonging to the trainer
        $courses = $trainer->courses()
            ->with(['modules', 'reviews', 'enrollments'])
            ->get();

        return view('trainer.profile', compact('trainer', 'courses'));
    }
}
