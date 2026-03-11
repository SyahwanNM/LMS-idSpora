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
            ->withAvg('reviews', 'rating')
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

        $course = \App\Models\Course::where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        $materials = \App\Models\CourseModule::where('course_id', $id)
            ->orderBy('order_no', 'asc')
            ->get();

        return view('trainer.content-studio', compact('course', 'materials'));
    }

    public function eventStudio($id)
    {
        $trainerId = \Illuminate\Support\Facades\Auth::id();

        $event = \App\Models\Event::where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        return view('trainer.event-studio', compact('event'));
    }

    public function saveEventQuiz(Request $request, $id)
    {

        $request->validate([
            'questions' => 'required|string',
            'passingGrade' => 'required|integer|min:0|max:100',
        ]);

        $trainerId = Auth::id();

        $event = \App\Models\Event::where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();


        $questionsData = json_decode($request->questions, true);

        if (empty($questionsData)) {
            return back()->with('error', 'Data soal tidak valid.');
        }

        $quiz = \App\Models\Quiz::updateOrCreate(
            ['event_id' => $event->id],
            ['passing_grade' => $request->passingGrade]
        );

        $quiz->questions()->delete();

        foreach ($questionsData as $q) {
            $quiz->questions()->create([
                'question_text' => $q['text'],
                'options' => json_encode($q['options']),
                'correct_answer_index' => $q['correctAnswer'],
                'weight' => $q['weight'] ?? 10,
            ]);
        }

        return redirect()->back()->with('success', 'Kuis event berhasil disimpan!');
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

    public function uploadCourseMaterials(Request $request, $id)
    {
        // 1. Validasi File & module_id
        $request->validate([
            'module_id' => 'required|exists:course_module,id',
            'file' => 'required|file|mimes:pdf,mp4,pptx,ppt,docx,doc,jpg,png,jpeg|max:512000'
        ]);

        $courseId = $id;
        $course = Course::findOrFail($courseId);

        // 2. Security Check: Pastikan yang upload adalah trainer pemilik kelas
        if ($course->trainer_id !== Auth::id()) {
            return back()->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk kelas ini.');
        }

        // 3. Ambil data modul "cangkang" yang sudah disiapkan Admin
        $module = CourseModule::where('id', $request->module_id)
            ->where('course_id', $courseId)
            ->firstOrFail();

        // 4. Proses Upload File
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Opsional: Hapus file fisik lama di storage jika sebelumnya trainer sudah pernah upload (Revisi)
            if ($module->content_url && Storage::disk('public')->exists($module->content_url)) {
                Storage::disk('public')->delete($module->content_url);
            }

            // Simpan file baru ke storage
            $filepath = $file->storeAs('courses/' . $courseId . '/materials', $filename, 'public');

            // 5. UPDATE data modul (BUKAN create baru)
            $module->update([
                'content_url' => $filepath,
                'file_name' => $filename,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            // 6. Ubah status Course menjadi pending_review agar masuk ke antrean Admin
            $course->update(['status' => 'pending_review']);

            return back()->with('success', 'File untuk materi "' . $module->title . '" berhasil diunggah dan masuk antrean review.');
        }

        return back()->with('error', 'Gagal mengunggah file. Silakan coba lagi.');
    }
    public function saveCourseQuiz(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'passingGrade' => 'required|integer|min:0|max:100',
            'questions' => 'required|json', // Data dikirim dalam bentuk JSON string dari Blade
        ]);

        $course = \App\Models\Course::findOrFail($id);

        // 2. Security Check
        if ($course->trainer_id !== \Illuminate\Support\Facades\Auth::id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        // 3. Decode JSON Data
        $questionsData = json_decode($request->questions, true);
        if (empty($questionsData)) {
            return back()->with('error', 'Kuis harus memiliki minimal 1 pertanyaan.');
        }

        // 4. Simpan atau Update Quiz Master
        $quiz = \App\Models\Quiz::updateOrCreate(
            ['course_id' => $course->id],
            ['passing_grade' => $request->passingGrade]
        );

        // 5. Hapus soal lama, lalu masukkan soal baru (Replace All)
        $quiz->questions()->delete();

        foreach ($questionsData as $index => $q) {
            $quiz->questions()->create([
                'question_text' => $q['text'],
                'options' => is_array($q['options']) ? json_encode($q['options']) : $q['options'],
                'correct_answer' => $q['correctAnswer'], // Sesuai dengan DB kamu
                'point_value' => $q['weight'] ?? 10,
                'order' => $index + 1,
            ]);
        }

        // 6. Ubah status Course menjadi pending_review agar di-cek Admin
        $course->update(['status' => 'pending_review']);

        return back()->with('success', 'Kuis berhasil disimpan dan otomatis masuk antrean review Admin!');
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
