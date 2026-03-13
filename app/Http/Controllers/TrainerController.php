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

    public function courseStudio(Request $request, $id)
    {
        $course = \App\Models\Course::where('id', $id)->where('trainer_id', Auth::id())->firstOrFail();

        // 1. Ambil semua modul course, lalu pecah per Bab (chunk 3)
        $unitIndex = $request->query('unit', 0); // Default ke Bab 1 (index 0)
        $allModules = \App\Models\CourseModule::where('course_id', $id)
            ->with([
                'quizQuestions' => function ($query) {
                    $query->orderBy('order_no', 'asc')->with(['answers' => function ($answerQuery) {
                        $answerQuery->orderBy('order_no', 'asc');
                    }]);
                }
            ])
            ->withCount('quizQuestions')
            ->orderBy('order_no', 'asc')
            ->get();
        $chunks = $allModules->chunk(3)->values();

        // 2. Ambil modul-modul HANYA untuk Bab yang dipilih
        $activeUnitModules = $chunks->get($unitIndex, collect());

        if ($activeUnitModules->isEmpty()) {
            return redirect()->route('trainer.courses')->with('error', 'Silabus untuk bab ini belum tersedia.');
        }

        $unitTitle = "Modul " . ($unitIndex + 1);

        return view('trainer.content-studio', compact('course', 'activeUnitModules', 'unitTitle', 'unitIndex'));
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
        $request->validate([
            'target_modules' => 'required|string', // Kumpulan ID modul di Bab ini
            'replace_module_id' => 'nullable|integer',
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:pdf,mp4,pptx,ppt,docx,doc,jpg,png,jpeg|max:512000'
        ]);

        $course = \App\Models\Course::findOrFail($id);
        if ($course->trainer_id !== Auth::id()) {
            return response()->json(['success' => false, 'error' => 'Akses ditolak.']);
        }

        // Ubah string "1,2" menjadi array [1, 2]
        $targetIds = collect(explode(',', $request->target_modules))
            ->map(fn($value) => (int) trim($value))
            ->filter(fn($value) => $value > 0)
            ->unique()
            ->values();

        if ($targetIds->isEmpty()) {
            return response()->json(['success' => false, 'error' => 'Target modul tidak valid.']);
        }

        $uploadedCount = 0;
        $rejectedFiles = [];

        if ($request->hasFile('files')) {
            $replaceModuleId = $request->input('replace_module_id');

            if (!empty($replaceModuleId)) {
                $replaceModule = \App\Models\CourseModule::where('course_id', $id)
                    ->where('id', (int) $replaceModuleId)
                    ->first();

                if (!$replaceModule || !$targetIds->contains((int) $replaceModuleId)) {
                    return response()->json(['success' => false, 'error' => 'File target penggantian tidak valid.']);
                }

                $files = $request->file('files');
                if (count($files) !== 1) {
                    return response()->json(['success' => false, 'error' => 'Mode ganti file hanya menerima 1 file.']);
                }

                $file = $files[0];
                $ext = strtolower($file->getClientOriginalExtension());
                $uploadType = in_array($ext, ['mp4']) ? 'video' : 'pdf';

                if ($uploadType !== $replaceModule->type) {
                    return response()->json(['success' => false, 'error' => 'Tipe file tidak sesuai dengan target yang dipilih.']);
                }

                $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());

                if ($replaceModule->content_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($replaceModule->content_url)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($replaceModule->content_url);
                }

                $filepath = $file->storeAs('courses/' . $id . '/materials', $filename, 'public');

                $replaceModule->update([
                    'content_url' => $filepath,
                    'file_name' => $filename,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);

                $course->update(['status' => 'pending_review']);

                return response()->json(['success' => true, 'message' => 'File berhasil diganti.']);
            }

            $modulesByType = \App\Models\CourseModule::where('course_id', $id)
                ->whereIn('id', $targetIds)
                ->get()
                ->groupBy('type')
                ->map(fn($group) => $group->values());

            foreach ($request->file('files') as $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                $type = in_array($ext, ['mp4']) ? 'video' : 'pdf';

                // Ambil slot silabus kosong di DALAM BAB INI sesuai tipe
                $typeModules = $modulesByType->get($type, collect());
                $moduleIndex = $typeModules->search(function ($candidate) {
                    return empty($candidate->content_url);
                });

                if ($moduleIndex !== false) {
                    $module = $typeModules->get($moduleIndex);
                    $typeModules->forget($moduleIndex);
                    $modulesByType->put($type, $typeModules->values());
                } else {
                    $module = null;
                }

                if ($module) {
                    $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());

                    if ($module->content_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($module->content_url)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($module->content_url);
                    }

                    $filepath = $file->storeAs('courses/' . $id . '/materials', $filename, 'public');

                    $module->update([
                        'content_url' => $filepath,
                        'file_name' => $filename,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                    $uploadedCount++;
                } else {
                    $suffix = ' (slot ' . strtoupper($type) . ' sudah terisi, pilih file di riwayat lalu klik GANTI untuk overwrite)';
                    $rejectedFiles[] = $file->getClientOriginalName() . $suffix;
                }
            }

            if ($uploadedCount > 0) {
                $course->update(['status' => 'pending_review']);
            }

            $msg = "$uploadedCount file berhasil diunggah.";
            if (count($rejectedFiles) > 0) {
                $msg .= " File (" . implode(", ", $rejectedFiles) . ") ditolak karena tipe tidak sesuai dengan silabus Bab ini.";
            }

            return response()->json(['success' => true, 'message' => $msg]);
        }
        return response()->json(['success' => false, 'error' => 'Tidak ada file.']);
    }
    public function saveCourseQuiz(Request $request, $id)
    {
        $request->validate([
            'quiz_module_id' => 'required|exists:course_module,id',
            'passingGrade' => 'required|integer|min:0|max:100',
            'questions' => 'required|array',
        ]);

        $course = \App\Models\Course::findOrFail($id);
        if ($course->trainer_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $questionsData = $request->questions;
        if (empty($questionsData)) {
            return response()->json(['success' => false, 'message' => 'Kuis minimal 1 soal.']);
        }

        // Kunci Quiz ke Slot Bab Ini
        $quizModule = \App\Models\CourseModule::where('id', $request->quiz_module_id)->where('course_id', $id)->firstOrFail();
        $quizModule->update(['content_url' => 'quiz_submitted']);

        // Delete old questions
        $quizModule->quizQuestions()->delete();

        // Create new questions
        foreach ($questionsData as $orderIndex => $questionData) {
            $quizQuestion = $quizModule->quizQuestions()->create([
                'question' => $questionData['text'],
                'points' => $questionData['weight'] ?? 10,
                'order_no' => $orderIndex + 1,
            ]);

            // Create answers for this question
            if (!empty($questionData['options']) && is_array($questionData['options'])) {
                foreach ($questionData['options'] as $optionIndex => $optionText) {
                    $quizQuestion->answers()->create([
                        'answer_text' => $optionText,
                        'is_correct' => ($optionIndex === (int)$questionData['correctAnswer']),
                        'order_no' => $optionIndex + 1,
                    ]);
                }
            }
        }

        $course->update(['status' => 'pending_review']);

        return response()->json(['success' => true, 'message' => 'Kuis Bab berhasil disimpan!']);
    }

    public function viewCourseMaterial($courseId, $moduleId)
    {
        $course = \App\Models\Course::where('id', $courseId)
            ->where('trainer_id', Auth::id())
            ->firstOrFail();

        $module = \App\Models\CourseModule::where('id', $moduleId)
            ->where('course_id', $course->id)
            ->firstOrFail();

        if (empty($module->content_url)) {
            abort(404, 'File materi tidak ditemukan.');
        }

        if (!Storage::disk('public')->exists($module->content_url)) {
            abort(404, 'File materi tidak tersedia di storage.');
        }

        $headers = [];
        if (!empty($module->mime_type)) {
            $headers['Content-Type'] = $module->mime_type;
        }

        $filePath = Storage::disk('public')->path($module->content_url);
        if (!file_exists($filePath)) {
            abort(404, 'File materi tidak tersedia di server.');
        }

        return response()->file($filePath, $headers);
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
