<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Quiz;
use App\Models\TrainerNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Event;
use App\Models\TrainerCertificate;
use Dompdf\Dompdf;
use Illuminate\Support\Str;

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

        $dashboardInvitations = TrainerNotification::query()
            ->where('trainer_id', $user->id)
            ->whereIn('type', ['course_invitation', 'event_invitation'])
            ->orderByRaw('CASE WHEN read_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $unreadInvitationCount = TrainerNotification::query()
            ->where('trainer_id', $user->id)
            ->whereIn('type', ['course_invitation', 'event_invitation'])
            ->whereNull('read_at')
            ->count();

        return view('trainer.dashboard', compact(
            'myCourses',
            'students',
            'totalCourses',
            'totalStudents',
            'dashboardInvitations',
            'unreadInvitationCount'
        ));
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

        $certifiedCourseIds = \App\Models\TrainerCertificate::query()
            ->where('trainer_id', $user->id)
            ->where('status', 'sent')
            ->where('certifiable_type', \App\Models\Course::class)
            ->whereNotNull('file_path')
            ->pluck('certifiable_id')
            ->map(fn($id) => (int) $id)
            ->all();

        $certifiedCourseIdSet = array_fill_keys($certifiedCourseIds, true);

        return view('trainer.courses', compact('courses', 'certifiedCourseIdSet'));
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

        $certifiedEventIds = \App\Models\TrainerCertificate::query()
            ->where('trainer_id', $user->id)
            ->where('status', 'sent')
            ->where('certifiable_type', \App\Models\Event::class)
            ->whereNotNull('file_path')
            ->pluck('certifiable_id')
            ->map(fn($id) => (int) $id)
            ->all();

        $certifiedEventIdSet = array_fill_keys($certifiedEventIds, true);

        return view('trainer.events', compact('events', 'upcomingCount', 'search', 'certifiedEventIdSet'));
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

        $query = \App\Models\Feedback::with(['user', 'event', 'replies.trainer'])
            ->whereIn('event_id', $eventIds)
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

        $statQuery = \App\Models\Feedback::whereIn('event_id', $eventIds);

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
                    $query->orderBy('order_no', 'asc')->with([
                        'answers' => function ($answerQuery) {
                            $answerQuery->orderBy('order_no', 'asc');
                        }
                    ]);
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
        $trainerId = Auth::id();

        $baseQuery = \App\Models\ManualPayment::query()
            ->with(['user:id,name', 'event:id,title,trainer_id', 'course:id,name,trainer_id'])
            ->where('status', 'settled')
            ->where(function ($query) use ($trainerId) {
                $query->whereHas('course', function ($courseQuery) use ($trainerId) {
                    $courseQuery->where('trainer_id', $trainerId);
                })->orWhereHas('event', function ($eventQuery) use ($trainerId) {
                    $eventQuery->where('trainer_id', $trainerId);
                });
            });

        $totalEarned = (clone $baseQuery)->sum('amount');
        $payments = (clone $baseQuery)
            ->latest('created_at')
            ->paginate(10);

        return view('trainer.finance', compact('totalEarned', 'payments'));
    }

    public function show()
    {
        $trainer = Auth::user();
        $courses = $trainer->coursesAsTrainer()
            ->with(['modules', 'reviews', 'enrollments', 'category'])
            ->withCount([
                'enrollments as active_enrollments_count' => function ($query) {
                    $query->where('status', 'active');
                },
                'modules'
            ])
            ->withAvg('reviews', 'rating')
            ->get();

        $courseIds = $courses->pluck('id');

        $totalStudents = $trainer->trainerEnrollments()
            ->where('enrollments.status', 'active')
            ->distinct('user_id')
            ->count('user_id');

        $eventIds = $trainer->eventsAsTrainer()->pluck('id');
        $feedbackQuery = \App\Models\Feedback::query();
        if ($eventIds->isNotEmpty()) {
            $feedbackQuery->whereIn('event_id', $eventIds);
        } else {
            $feedbackQuery->whereRaw('1 = 0');
        }

        $averageRating = round((clone $feedbackQuery)->avg('rating') ?? 0, 1);

        $recentFeedbacks = (clone $feedbackQuery)
            ->with(['user:id,name', 'replies.trainer'])
            ->latest('created_at')
            ->take(3)
            ->get();

        $upcomingEvents = $trainer->eventsAsTrainer()
            ->whereDate('event_date', '>=', now()->toDateString())
            ->withCount([
                'registrations as participants_count' => function ($query) {
                    $query->where('status', 'active');
                }
            ])
            ->orderBy('event_date', 'asc')
            ->take(3)
            ->get();

        $paymentsQuery = \App\Models\ManualPayment::query()
            ->with(['course:id,name,trainer_id', 'event:id,title,trainer_id'])
            ->where('status', 'settled')
            ->where(function ($query) use ($trainer) {
                $query->whereHas('course', function ($courseQuery) use ($trainer) {
                    $courseQuery->where('trainer_id', $trainer->id);
                })->orWhereHas('event', function ($eventQuery) use ($trainer) {
                    $eventQuery->where('trainer_id', $trainer->id);
                });
            });

        $totalEarned = (clone $paymentsQuery)->sum('amount');
        $ledgerPayments = (clone $paymentsQuery)
            ->latest('created_at')
            ->take(3)
            ->get();

        $expertiseTags = $courses
            ->pluck('category.name')
            ->filter()
            ->unique()
            ->values()
            ->take(6);

        if ($expertiseTags->isEmpty() && !empty($trainer->profession)) {
            $expertiseTags = collect(explode(' ', strtoupper($trainer->profession)))
                ->filter()
                ->take(4)
                ->values();
        }

        if ($expertiseTags->isEmpty()) {
            $expertiseTags = collect(['TRAINING', 'MENTORING']);
        }

        // Additional stats for enhanced profile
        $totalCourses = $courses->count();
        $totalEvents = $trainer->eventsAsTrainer()->count();
        $totalFeedbacks = (clone $feedbackQuery)->count();
        $topCourses = $courses->sortByDesc('reviews_avg_rating')->take(3);

        return view('trainer.profile', compact(
            'trainer',
            'courses',
            'totalStudents',
            'averageRating',
            'recentFeedbacks',
            'upcomingEvents',
            'totalEarned',
            'ledgerPayments',
            'expertiseTags',
            'totalCourses',
            'totalEvents',
            'totalFeedbacks',
            'topCourses'
        ));
    }

    public function editProfile()
    {
        $trainer = Auth::user();

        return view('trainer.profile-edit', compact('trainer'));
    }

    public function updateProfile(Request $request)
    {
        $trainer = Auth::user();

        // Check if this is avatar-only upload (AJAX or file input only)
        $isAvatarOnly = $request->hasFile('avatar') && !$request->filled('name');

        $rules = [
            'phone' => 'nullable|string|max:30',
            'profession' => 'nullable|string|max:100',
            'institution' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ];

        if (!$isAvatarOnly) {
            $rules['name'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('avatar') || $request->hasFile('avatar_file')) {
            $avatarFile = $request->file('avatar') ?? $request->file('avatar_file');

            if ($avatarFile) {
                // Delete old avatar
                if (!empty($trainer->avatar) && !str_starts_with((string) $trainer->avatar, 'http')) {
                    $oldPath = str_starts_with((string) $trainer->avatar, 'avatars/')
                        ? (string) $trainer->avatar
                        : 'avatars/' . $trainer->avatar;

                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $filename = uniqid('ava_') . '.' . $avatarFile->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('avatars', $avatarFile, $filename);
                $validated['avatar'] = 'avatars/' . $filename;
            }
        } else {
            unset($validated['avatar']);
        }

        $trainer->update($validated);

        // Return JSON for AJAX avatar uploads
        if ($isAvatarOnly) {
            return response()->json([
                'success' => true,
                'avatar_url' => $trainer->avatar_url,
                'message' => 'Foto profil berhasil diperbarui.'
            ]);
        }

        return redirect()->route('trainer.profile')->with('success', 'Profil trainer berhasil diperbarui.');
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

    public function uploadEventMaterials(Request $request, $id)
    {
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:pdf,mp4,pptx,ppt,docx,doc,jpg,png,jpeg|max:512000'
        ]);

        $event = \App\Models\Event::where('id', $id)
            ->where('trainer_id', Auth::id())
            ->firstOrFail();

        if (!$request->hasFile('files')) {
            return response()->json(['success' => false, 'error' => 'Tidak ada file.']);
        }

        $storedFiles = [];
        $latestImagePath = null;

        foreach ($request->file('files') as $file) {
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $filepath = $file->storeAs('events/' . $event->id . '/materials', $filename, 'public');

            $storedFiles[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $filepath,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];

            if (str_starts_with((string) $file->getMimeType(), 'image/')) {
                $latestImagePath = $filepath;
            }
        }

        if ($latestImagePath) {
            $event->update(['vbg_path' => $latestImagePath]);
        }

        return response()->json([
            'success' => true,
            'message' => count($storedFiles) . ' file berhasil diunggah.',
            'files' => $storedFiles,
        ]);
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
                        'is_correct' => ($optionIndex === (int) $questionData['correctAnswer']),
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

    public function certificatesIndex()
    {
        $trainer = Auth::user();

        $context = (string) request()->query('context', '');
        $targetId = (int) request()->query('id', 0);

        $finishedEvents = Event::query()
            ->where('trainer_id', $trainer->id)
            ->whereNotNull('event_date')
            ->whereDate('event_date', '<', now()->toDateString())
            ->orderByDesc('event_date')
            ->get(['id', 'title', 'jenis', 'event_date']);

        $finishedCourses = Course::query()
            ->where('trainer_id', $trainer->id)
            ->where('status', 'approved')
            ->whereNotNull('approved_at')
            ->where('approved_at', '<', now())
            ->orderByDesc('approved_at')
            ->get(['id', 'name', 'approved_at', 'status']);

        $certificates = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->where('status', 'sent')
            ->get(['certifiable_type', 'certifiable_id', 'certificate_number', 'issued_at', 'file_path', 'type_code']);

        $certMap = [];
        foreach ($certificates as $cert) {
            $key = $cert->certifiable_type . ':' . (int) $cert->certifiable_id;
            $certMap[$key] = $cert;
        }

        $historyItems = collect();

        foreach ($finishedEvents as $event) {
            $key = Event::class . ':' . (int) $event->id;
            $cert = $certMap[$key] ?? null;
            $historyItems->push([
                'type' => 'event',
                'id' => (int) $event->id,
                'title' => $event->title,
                'date' => $event->event_date,
                'statusLabel' => 'Selesai',
                'certificate' => $cert,
                'downloadUrl' => $cert ? route('trainer.certificates.events.download', $event) : null,
                'highlight' => $context === 'event' && $targetId === (int) $event->id,
            ]);
        }

        foreach ($finishedCourses as $course) {
            $key = Course::class . ':' . (int) $course->id;
            $cert = $certMap[$key] ?? null;
            $historyItems->push([
                'type' => 'course',
                'id' => (int) $course->id,
                'title' => $course->name,
                'date' => $course->approved_at,
                'statusLabel' => 'Selesai',
                'certificate' => $cert,
                'downloadUrl' => $cert ? route('trainer.certificates.courses.download', $course) : null,
                'highlight' => $context === 'course' && $targetId === (int) $course->id,
            ]);
        }

        $historyItems = $historyItems->sortByDesc(fn ($item) => $item['date'] ?? now());

        return view('trainer.certificates.index', [
            'historyItems' => $historyItems,
        ]);
    }

    public function certificateEventShow(Request $request, Event $event)
    {
        $trainer = Auth::user();
        $trainerCert = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->where('status', 'sent')
            ->where('certifiable_type', Event::class)
            ->where('certifiable_id', $event->id)
            ->latest('issued_at')
            ->firstOrFail();

        $issuedAt = $trainerCert->issued_at ?? now();
        $data = $this->buildTrainerCertificateDataFromEvent($request, $event, $trainer, $issuedAt);
        $data['certificateNumber'] = $trainerCert->certificate_number;
        $data['roleLabel'] = $this->certificateTypeLabel((string) $trainerCert->type_code);

        return view('trainer.certificates.show', $data);
    }

    public function certificateEventDownload(Request $request, Event $event)
    {
        $trainer = Auth::user();
        $trainerCert = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->where('status', 'sent')
            ->where('certifiable_type', Event::class)
            ->where('certifiable_id', $event->id)
            ->latest('issued_at')
            ->firstOrFail();

        if (!empty($trainerCert->file_path)) {
            $absolutePath = storage_path('app/' . $trainerCert->file_path);
            if (is_file($absolutePath)) {
                $filename = 'Sertifikat_Trainer_' . Str::slug($event->title) . '_' . Str::slug($trainer->name) . '.pdf';
                return response()->download($absolutePath, $filename, [
                    'Content-Type' => 'application/pdf',
                ]);
            }
        }

        abort(404, 'File sertifikat belum tersedia.');
    }

    public function certificateCourseShow(Request $request, Course $course)
    {
        $trainer = Auth::user();
        $trainerCert = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->where('status', 'sent')
            ->where('certifiable_type', Course::class)
            ->where('certifiable_id', $course->id)
            ->latest('issued_at')
            ->firstOrFail();

        $issuedAt = $trainerCert->issued_at ?? now();
        $data = $this->buildTrainerCertificateDataFromCourse($request, $course, $trainer, $issuedAt);
        $data['certificateNumber'] = $trainerCert->certificate_number;
        $data['roleLabel'] = $this->certificateTypeLabel((string) $trainerCert->type_code);

        return view('trainer.certificates.show', $data);
    }

    public function certificateCourseDownload(Request $request, Course $course)
    {
        $trainer = Auth::user();
        $trainerCert = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->where('status', 'sent')
            ->where('certifiable_type', Course::class)
            ->where('certifiable_id', $course->id)
            ->latest('issued_at')
            ->firstOrFail();

        if (!empty($trainerCert->file_path)) {
            $absolutePath = storage_path('app/' . $trainerCert->file_path);
            if (is_file($absolutePath)) {
                $filename = 'Sertifikat_Trainer_' . Str::slug($course->name) . '_' . Str::slug($trainer->name) . '.pdf';
                return response()->download($absolutePath, $filename, [
                    'Content-Type' => 'application/pdf',
                ]);
            }
        }

        abort(404, 'File sertifikat belum tersedia.');
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

    private function buildIdsporaCertificateNumber(string $activityCode, string $typeCode, string $sequence, \Carbon\CarbonInterface $issuedAt): string
    {
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        $monthRoman = $romanMonths[(int) $issuedAt->format('n')] ?? '';
        $year = $issuedAt->format('Y');
        $seqDigits = preg_replace('/\D+/', '', $sequence) ?: '1';
        $seq = str_pad(substr($seqDigits, -3), 3, '0', STR_PAD_LEFT);

        $activity = strtoupper(trim($activityCode ?: 'WBN'));
        $type = strtoupper(trim($typeCode ?: 'TRN'));

        return "IDSP/{$activity}/{$type}/{$seq}/{$monthRoman}/{$year}";
    }

    private function extractEventAssetsBase64(Event $event): array
    {
        $logos = [];
        foreach (is_array($event->certificate_logo) ? $event->certificate_logo : [] as $l) {
            $path = str_replace('storage/', '', (string) $l);
            if ($path !== '' && Storage::disk('public')->exists($path)) {
                $absolutePath = Storage::disk('public')->path($path);
                $mime = (is_string($absolutePath) && is_file($absolutePath)) ? (mime_content_type($absolutePath) ?: 'application/octet-stream') : 'application/octet-stream';
                $logos[] = 'data:' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($path));
            }
        }

        $sigs = [];
        foreach (is_array($event->certificate_signature) ? $event->certificate_signature : [] as $s) {
            $path = str_replace('storage/', '', (string) $s);
            if ($path !== '' && Storage::disk('public')->exists($path)) {
                $absolutePath = Storage::disk('public')->path($path);
                $mime = (is_string($absolutePath) && is_file($absolutePath)) ? (mime_content_type($absolutePath) ?: 'application/octet-stream') : 'application/octet-stream';
                $sigs[] = 'data:' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($path));
            }
        }

        return [$logos, $sigs];
    }

    private function buildTrainerCertificateDataFromEvent(Request $request, Event $event, $trainer, \Carbon\CarbonInterface $issuedAt): array
    {
        [$logosBase64, $signaturesBase64] = $this->extractEventAssetsBase64($event);

        $activityCodeMap = [
            'webinar' => 'WBN',
            'seminar' => 'SMN',
            'workshop' => 'WRT',
            'training' => 'WRT',
            'video' => 'VDP',
            'e-learning' => 'ELR',
            'elearning' => 'ELR',
        ];
        $jenis = strtolower((string) ($event->jenis ?? ''));
        $defaultActivityCode = $activityCodeMap[$jenis] ?? 'WBN';

        $activityCode = (string) $request->query('activity', $defaultActivityCode);
        $typeCode = (string) $request->query('type', 'TRN');
        $sequence = (string) $request->query('seq', '001');

        $certificateNumber = $this->buildIdsporaCertificateNumber($activityCode, $typeCode, $sequence, $issuedAt);

        return [
            'context' => 'event',
            'event' => $event,
            'course' => null,
            'user' => $trainer,
            'issuedAt' => $issuedAt,
            'certificateNumber' => $certificateNumber,
            'logosBase64' => $logosBase64,
            'signaturesBase64' => $signaturesBase64,
            'roleLabel' => $this->certificateTypeLabel($typeCode),
        ];
    }

    private function buildTrainerCertificateDataFromCourse(Request $request, Course $course, $trainer, \Carbon\CarbonInterface $issuedAt): array
    {
        $activityCode = (string) $request->query('activity', 'ELR');
        $typeCode = (string) $request->query('type', 'TRN');
        $sequence = (string) $request->query('seq', '001');

        $certificateNumber = $this->buildIdsporaCertificateNumber($activityCode, $typeCode, $sequence, $issuedAt);

        return [
            'context' => 'course',
            'event' => null,
            'course' => $course,
            'user' => $trainer,
            'issuedAt' => $issuedAt,
            'certificateNumber' => $certificateNumber,
            'logosBase64' => [],
            'signaturesBase64' => [],
            'roleLabel' => $this->certificateTypeLabel($typeCode),
        ];
    }

    private function certificateTypeLabel(string $typeCode): string
    {
        $map = [
            'SRT' => 'Peserta',
            'MC' => 'MC',
            'TRN' => 'Narasumber',
            'PNT' => 'Panitia',
            'CLB' => 'Kolaborator',
            'MOD' => 'Moderator',
            'GRD' => 'Kelulusan',
            'SPV' => 'Supervisor/penilai',
        ];
        $key = strtoupper(trim($typeCode));
        return $map[$key] ?? $key;
    }

    public function storeFeedbackReply(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $validated = $request->validate([
            'feedback_id' => 'required|exists:feedback,id',
            'response' => 'required|string|min:3|max:5000',
        ]);

        // Verify that the feedback belongs to an event the trainer manages
        $feedback = \App\Models\Feedback::findOrFail($validated['feedback_id']);

        $trainerHasAccess = \App\Models\Event::where('id', $feedback->event_id)
            ->where('trainer_id', $user->id)
            ->exists();

        if (!$trainerHasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this feedback'
            ], 403);
        }

        // Create the reply
        $reply = \App\Models\FeedbackReply::create([
            'feedback_id' => $validated['feedback_id'],
            'trainer_id' => $user->id,
            'response' => $validated['response'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply saved successfully',
            'data' => $reply
        ]);
    }
}
