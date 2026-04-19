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
            'trainer_rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:1|max:1000',
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
            'trainer_rating' => $request->trainer_rating,
            'comment' => $request->comment,
        ]);

        // Logic for auto certificate issuance if already 100% completed
        $enrollment = \App\Models\Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment) {
            $enrollment->setRelation('course', $course);
        }

        if ($enrollment && $enrollment->isFullyCompleted()) {
            $enrollment->update([
                'status' => 'completed',
                'completed_at' => $enrollment->completed_at ?? now(),
                'certificate_issued_at' => $enrollment->certificate_issued_at ?? now(),
            ]);

            if (empty($enrollment->certificate_number)) {
                $enrollment->update([
                    'certificate_number' => \App\Http\Controllers\CRM\CertificateController::generateCertificateNumberCourse($course, $enrollment)
                ]);
            }
        }

        return redirect()->route('course.certificate', $course->id)
            ->with('success', 'Penilaian berhasil disimpan. Sertifikat Anda siap dicek.');
    }
}
