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
        $course = Course::with(['modules', 'enrollments'])
            ->where('id', $id)
            ->where('trainer_id', Auth::id())
            ->firstOrFail();

        return view('trainer.detail-course', compact('course'));
    }

    public function finance()
    {
        $user = Auth::user();

        // 1. Ambil Total Pendapatan (Hanya angka total)
        $totalEarned = $user->trainerPayments()->sum('amount');

        // 2. Ambil Riwayat Pembayaran (List detail)
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

    /**
     * Handle materials upload for a course
     */
    public function uploadMaterials(Request $request)
    {
        $request->validate([
            'courseId' => 'required|integer',
            'files.*' => 'required|file|mimes:pdf,mp4,pptx,ppt,docx,doc,jpg,png,jpeg|max:102400'
        ]);

        $courseId = $request->input('courseId');
        $course = Course::findOrFail($courseId);

        // Check if trainer owns this course
        if ($course->trainer_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $uploadedFiles = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('courses/' . $courseId . '/materials', $filename, 'public');

                // Create course module record
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

    /**
     * Save quiz for a course
     */
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

        // Check if trainer owns this course
        if ($course->trainer_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Create or update quiz
        $quiz = Quiz::firstOrCreate(
            ['course_id' => $courseId],
            ['passing_grade' => $request->input('passingGrade')]
        );

        $quiz->update(['passing_grade' => $request->input('passingGrade')]);

        // Store questions (this depends on your QuizQuestion model)
        // Adjust based on your actual database structure
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

    /**
     * Determine module type based on file extension
     */
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
