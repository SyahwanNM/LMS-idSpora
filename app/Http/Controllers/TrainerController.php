<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TrainerController extends Controller
{
    /**
     * Show trainer profile with their courses
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get courses belonging to the trainer
        $myCourses = $user->coursesAsTrainer()
            ->withCount([
                'enrollments' => function ($query) {
                    $query->where('enrollments.status', 'active');
                }
            ])
            ->get();

        $students = $user->trainerEnrollments()
            ->with(['student', 'course'])
            ->orderBy('enrollments.created_at', 'desc')
            ->paginate(10);

        $totalCourses = $myCourses->count();
        $totalStudents = $user->trainerEnrollments()
            ->where('enrollments.status', 'active')
            ->distinct('user_id')
            ->count('user_id');

        return view('trainer.dashboard', compact('myCourses', 'students', 'totalCourses', 'totalStudents'));
    }

    public function courses()
    {
        $user = Auth::user();

        $courses = $user->coursesAsTrainer()
            ->withCount([
                'enrollments' => function ($query) {
                    $query->where('status', 'active');
                },
                'modules' // Tambahkan ini untuk menghitung jumlah modul
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('trainer.courses', compact('courses'));
    }

    public function courseDetail($id)
    {
        $trainerId = \Illuminate\Support\Facades\Auth::id();

        // 1. Ambil data course dan relasinya
        $course = \App\Models\Course::with([
            'modules' => function ($query) {
                // Urutkan modul, dan load relasi kuis
                $query->orderBy('order_no', 'asc')->with('quizQuestions');
            },
            'enrollments.student',
            'reviews'
        ])
            ->where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        // 2. Hitung statistik dasar
        $enrollmentCount = $course->enrollments->where('status', 'active')->count();
        $averageRating = number_format($course->reviews->avg('rating') ?? 0, 1);
        $moduleCount = $course->modules->count();
        $activeStudents = $course->enrollments->where('status', 'active');

        // 3. Kalkulasi Quiz Recap (Berdasarkan model QuizAttempt)
        $totalSubmissions = 0;
        $totalScores = 0;
        $classAverage = 0;

        // Ambil semua percobaan kuis (QuizAttempt) yang terkait dengan modul-modul di course ini
        $moduleIds = $course->modules->pluck('id');
        $quizAttempts = \App\Models\QuizAttempt::with(['user', 'courseModule'])
            ->whereIn('course_module_id', $moduleIds)
            ->orderBy('completed_at', 'desc')
            ->get();

        if ($quizAttempts->count() > 0) {
            $totalSubmissions = $quizAttempts->count();

            // Hitung rata-rata persentase kelas
            foreach ($quizAttempts as $attempt) {
                $totalScores += $attempt->percentage;
            }
            $classAverage = round($totalScores / $totalSubmissions, 1);
        }

        return view('trainer.detail-course', compact(
            'course',
            'enrollmentCount',
            'averageRating',
            'moduleCount',
            'activeStudents',
            'quizAttempts',
            'classAverage',
            'totalSubmissions'
        ));
    }
    public function events(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $search = $request->query('search');

        $query = \App\Models\Event::where('trainer_id', $user->id)
            ->withCount([
                'registrations as participants_count' => function ($q) {
                    $q->where('status', 'active');
                }
            ]);

        if ($search) {
            $query->where('title', 'LIKE', "%{$search}%");
        }

        $events = $query->orderBy('event_date', 'asc')->paginate(9);

        $upcomingCount = \App\Models\Event::where('trainer_id', $user->id)
            ->where('event_date', '>=', now())
            ->count();

        return view('trainer.events', compact('events', 'upcomingCount', 'search'));
    }

    public function eventDetail($id)
    {
        $trainerId = \Illuminate\Support\Facades\Auth::id();

        $event = \App\Models\Event::where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        return view('trainer.detail-event', compact('event'));
    }

    public function feedback(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $eventIds = \App\Models\Event::where('trainer_id', $user->id)->pluck('id');
        $courseIds = \App\Models\Course::where('trainer_id', $user->id)->pluck('id');

        $query = \App\Models\Feedback::with(['user', 'event', 'course'])
            ->where(function ($q) use ($eventIds, $courseIds) {
                $q->whereIn('event_id', $eventIds)
                    ->orWhereIn('course_id', $courseIds);
            })
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%");
                })->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        $feedbacks = $query->paginate(10);

        $statQuery = \App\Models\Feedback::where(function ($q) use ($eventIds, $courseIds) {
            $q->whereIn('event_id', $eventIds)->orWhereIn('course_id', $courseIds);
        });

        $totalFeedbacks = $statQuery->count();
        $averageRating = 0;
        $satisfactionRate = 0;
        $ratingStats = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

        if ($totalFeedbacks > 0) {
            $averageRating = round((clone $statQuery)->avg('rating'), 1);

            $ratingsCount = (clone $statQuery)
                ->selectRaw('rating, count(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating')
                ->toArray();

            foreach ($ratingsCount as $star => $count) {
                if (isset($ratingStats[$star])) {
                    $ratingStats[$star] = round(($count / $totalFeedbacks) * 100);
                }
            }

            $satisfactionRate = $ratingStats[5] + $ratingStats[4];
        }

        return view('trainer.feedback', compact(
            'feedbacks',
            'totalFeedbacks',
            'averageRating',
            'ratingStats',
            'satisfactionRate'
        ));
    }

    public function courseStudio($id)
    {
        $trainerId = \Illuminate\Support\Facades\Auth::id();

        // 1. Ambil data course
        $course = \App\Models\Course::where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        // 2. Ambil data materials/modules milik course ini
        $materials = \App\Models\CourseModule::where('course_id', $id)
            ->orderBy('order_no', 'asc')
            ->get();

        // 3. Kirimkan ke view (termasuk $materials)
        return view('trainer.content-studio', compact('course', 'materials'));
    }

    public function eventStudio($id)
    {
        $trainerId = \Illuminate\Support\Facades\Auth::id();

        // Cari event, pastikan ini milik trainer yang sedang login
        $event = \App\Models\Event::where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        // Tampilkan halaman khusus studio event
        return view('trainer.event-studio', compact('event'));
    }

    public function finance()
    {
        $user = Auth::user();

        $totalEarned = $user->trainerPayments()->sum('amount');
        $payments = $user->trainerPayments()
            ->orderBy('payment_date', 'desc')
            ->paginate(10);

        return view('trainer.finance', compact('totalEarned', 'payments'));
    }

    public function show()
    {
        $trainer = Auth::user();
        $courses = $trainer->coursesAsTrainer()
            ->with(['modules', 'reviews', 'enrollments'])
            ->get();

        return view('trainer.profile', compact('trainer', 'courses'));
    }

    public function uploadMaterials(Request $request)
    {
        $request->validate([
            'courseId' => 'required|integer',
            'files.*' => 'required|file|mimes:pdf,mp4,pptx,ppt,docx,doc,jpg,png,jpeg|max:102400'
        ]);

        $courseId = $request->input('courseId');
        $course = Course::findOrFail($courseId);

        if ($course->trainer_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $uploadedFiles = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('courses/' . $courseId . '/materials', $filename, 'public');

                CourseModule::create([
                    'course_id' => $courseId,
                    'title' => $file->getClientOriginalName(),
                    'description' => null,
                    'type' => $this->getModuleType($file->getClientOriginalExtension()),
                    'content' => $filepath,
                    'order' => CourseModule::where('course_id', $courseId)->max('order') + 1 ?? 1,
                ]);

                $uploadedFiles[] = [
                    'filename' => $filename,
                    'type' => $file->getClientOriginalExtension(),
                    'size' => $file->getSize(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Materials uploaded successfully',
            'files' => $uploadedFiles
        ]);
    }
    public function saveQuiz(Request $request)
    {
        $request->validate([
            'courseId' => 'required|integer',
            'passingGrade' => 'required|integer|min:0|max:100',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.correctAnswer' => 'required|integer',
            'questions.*.weight' => 'required|integer|min:1',
        ]);

        $courseId = $request->input('courseId');
        $course = Course::findOrFail($courseId);

        if ($course->trainer_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $quiz = Quiz::firstOrCreate(
            ['course_id' => $courseId],
            ['passing_grade' => $request->input('passingGrade')]
        );

        $quiz->update(['passing_grade' => $request->input('passingGrade')]);

        $quiz->questions()->delete();

        foreach ($request->input('questions') as $index => $question) {
            $quiz->questions()->create([
                'question_text' => $question['text'],
                'options' => $question['options'],
                'correct_answer' => $question['correctAnswer'],
                'point_value' => $question['weight'],
                'order' => $index + 1,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Quiz saved successfully',
            'quizId' => $quiz->id,
            'totalQuestions' => count($request->input('questions')),
            'totalPoints' => collect($request->input('questions'))->sum('weight'),
        ]);
    }

    private function getModuleType($extension)
    {
        return match (strtolower($extension)) {
            'pdf' => 'document',
            'doc', 'docx' => 'document',
            'ppt', 'pptx' => 'presentation',
            'mp4' => 'video',
            'jpg', 'jpeg', 'png' => 'image',
            default => 'file',
        };
    }
}
