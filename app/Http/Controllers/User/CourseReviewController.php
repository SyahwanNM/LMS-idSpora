<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseReviewController extends Controller
{
    public function create(Course $course)
    {
        $user = Auth::user();
        
        // Cek jika user sudah memberikan review
        $exists = Review::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            return redirect()->route('dashboard')
                ->with('info', 'Anda sudah memberikan penilaian untuk course ini.');
        }

        return view('course.rating-course', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // Cek kembali jika sudah ada
        $exists = Review::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            return redirect()->route('dashboard')
                ->with('info', 'Anda sudah memberikan penilaian untuk course ini.');
        }

        // Simpan review
        Review::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('dashboard')->with('success', 'Penilaian Anda berhasil disimpan. Terima kasih atas feedback Anda!');
    }
}
