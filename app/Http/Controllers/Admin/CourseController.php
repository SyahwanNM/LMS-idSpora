<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\Enrollment;
use App\Models\ManualPayment;

use App\Models\QuizAttempt;
use App\Models\Progress;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\TrainerNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Show the payment page for a course.
     */
    public function payment(Request $request, Course $course)
    {
        $this->ensureCoursePublishedForPublic($request->user(), $course);
        // Use the custom payment-course view for payment page
        return view('course.payment-course', compact('course'));
    }

    /**
     * Show the learning page for an enrolled/purchased course.
     */
    public function learn(Request $request, Course $course)
    {
        $user = $request->user();

        $this->ensureCoursePublishedForPublic($user, $course);

        $enrollment = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $enrolledActive = $enrollment && $enrollment->status === 'active';

        $hasSettledPayment = ManualPayment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'settled')
            ->exists();

        // Only allow access if enrollment is active OR payment already approved.
        // Pending payment/enrollment should not pass.
        if (!$enrolledActive && !$hasSettledPayment) {
            return redirect()->route('course.detail', $course->id)
                ->with('error', 'Silakan lakukan pembelian course terlebih dahulu.');
        }

        // If payment is settled but enrollment isn't active yet, auto-activate it.
        if (!$enrolledActive && $hasSettledPayment) {
            $enrollment = Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                ['status' => 'active']
            );
            if ($enrollment->status !== 'active') {
                $enrollment->status = 'active';
                $enrollment->save();
            }
        }

        $course->load([
            'modules' => function ($q) {
                $q->orderBy('order_no');
            },
            'modules.quizQuestions',
        ]);

        $modules = $course->modules;
        $selectedId = $request->query('module');
        $currentModule = null;

        $isFreeCourse = (int) ($course->price ?? 0) <= 0;
        $freeAccessMode = $isFreeCourse ? (string) ($course->free_access_mode ?? 'limit_2') : 'all';
        $freeAccessibleModuleIds = [];
        if ($isFreeCourse && $freeAccessMode === 'limit_2') {
            $freeAccessibleModuleIds = $modules
                ->sortBy('order_no')
                ->values()
                ->take(2)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->values()
                ->all();
        }

        if ($selectedId) {
            $currentModule = $modules->firstWhere('id', (int) $selectedId);
        }
        if (!$currentModule) {
            $currentModule = $modules->first();
        }

        // Free course access policy: optionally limit to first 2 modules
        if ($isFreeCourse && $freeAccessMode === 'limit_2' && $currentModule) {
            $allowed = in_array((int) $currentModule->id, $freeAccessibleModuleIds, true);
            if (!$allowed) {
                $fallbackId = $freeAccessibleModuleIds[0] ?? (int) ($modules->first()?->id ?? 0);
                if ($fallbackId > 0) {
                    $target = route('course.learn', $course->id) . '?module=' . $fallbackId;
                    return redirect()->to($target)
                        ->with('error', 'Course gratis ini hanya membuka 2 modul pertama.');
                }
            }
        }

        // Gate: only lock the module immediately after an unpassed quiz
        $passingPercent = 75;
        if ($currentModule && $currentModule->order_no) {
            $prevModule = $modules
                ->filter(fn($m) => (int) ($m->order_no ?? 0) < (int) $currentModule->order_no)
                ->sortByDesc('order_no')
                ->first();

            if ($prevModule && strtolower(trim((string) ($prevModule->type ?? ''))) === 'quiz') {
                $lastAttempt = QuizAttempt::query()
                    ->where('user_id', $user->id)
                    ->where('course_module_id', $prevModule->id)
                    ->whereNotNull('completed_at')
                    ->orderByDesc('completed_at')
                    ->first();

                $passedPrevQuiz = $lastAttempt ? $lastAttempt->isPassed($passingPercent) : false;

                if (!$passedPrevQuiz) {
                    $target = route('course.learn', $course->id) . '?module=' . $prevModule->id;
                    return redirect()->to($target)
                        ->with('error', 'Kamu harus lulus kuis terlebih dahulu untuk membuka materi selanjutnya.');
                }
            }
        }

        // Progress tracking:
        // - video/pdf modules are marked completed once opened
        // - quiz modules are marked completed only if user has passed
        if ($enrollment && $currentModule) {
            $moduleType = strtolower(trim((string) ($currentModule->type ?? '')));
            $markCompleted = $moduleType !== 'quiz';

            if (!$markCompleted && $moduleType === 'quiz') {
                $markCompleted = QuizAttempt::query()
                    ->where('user_id', $user->id)
                    ->where('course_module_id', $currentModule->id)
                    ->whereNotNull('completed_at')
                    ->where('total_questions', '>', 0)
                    ->whereRaw('(correct_answers * 100) >= (total_questions * ?)', [$passingPercent])
                    ->exists();
            }

            if ($markCompleted) {
                Progress::query()->updateOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'course_module_id' => $currentModule->id,
                    ],
                    [
                        'completed' => true,
                    ]
                );
            }
        }

        return view('course.modul-course', [
            'course' => $course,
            'modules' => $modules,
            'currentModule' => $currentModule,
            'freeAccessMode' => $freeAccessMode,
            'freeAccessibleModuleIds' => $freeAccessibleModuleIds,
        ]);
    }
    /**
     * Publish course: set status to 'active' (complete)
     */
    public function publish(Request $request, Course $course)
    {
        if (((string) $course->status) === 'active') {
            return redirect()
                ->route('admin.courses.index')
                ->with('already_published', true);
        }

        // Compute material completeness (server-side safety net; UI also warns before publish)
        $totalModules = (int) $course->modules()->count();
        $pdfCount = (int) $course->modules()->where('type', 'pdf')->count();
        $videoCount = (int) $course->modules()->where('type', 'video')->count();
        $quizCount = (int) $course->modules()->where('type', 'quiz')->count();

        $missing = [];
        if ($totalModules <= 0) {
            $missing[] = 'Modul';
        }
        if ($pdfCount <= 0) {
            $missing[] = 'Modul (PDF)';
        }
        if ($videoCount <= 0) {
            $missing[] = 'Video';
        }
        if ($quizCount <= 0) {
            $missing[] = 'Kuis';
        }

        $course->status = 'active';
        $course->save();

        $redirect = redirect()->route('admin.courses.index')
            ->with('success', 'Course berhasil diterbitkan!');

        if (!empty($missing)) {
            $redirect->with('publish_warning', $missing);
        }

        return $redirect;
    }

    /**
     * Strip HTML tags and normalize whitespace from description input.
     */
    private function sanitizeDescription(?string $html): string
    {
        $text = strip_tags((string) $html);
        // Normalize whitespace to single spaces and trim
        $text = preg_replace('/\s+/u', ' ', $text ?? '');
        return trim($text ?? '');
    }

    private function notifyTrainerCourseInvitation(Course $course, int $trainerId, string $source = 'trainer_id'): void
    {
        $trainer = User::query()
            ->where('id', $trainerId)
            ->where('role', 'trainer')
            ->first();

        if (!$trainer) {
            return;
        }

        TrainerNotification::create([
            'trainer_id' => $trainer->id,
            'type' => 'course_invitation',
            'title' => 'Undangan Menjadi Trainer Course',
            'message' => 'Anda diundang menjadi trainer untuk course "' . $course->name . '".',
            'data' => [
                'entity_type' => 'course',
                'entity_id' => $course->id,
                'url' => route('trainer.detail-course', $course->id),
                'invitation_status' => 'pending',
                'invitation_source' => $source,
                'due_at' => now()->addDays(7)->toIso8601String(),
            ],
        ]);
    }

    /**
     * Attempt to probe video duration in seconds using ffprobe (if available).
     */
    private function probeVideoDurationSeconds(string $absolutePath): ?int
    {
        // Prefer explicit env/config override
        $configured = config('media.ffprobe_path');
        $arg = escapeshellarg($absolutePath);
        $bins = [];
        if (is_string($configured) && $configured !== '') {
            $bins[] = $configured;
        }
        // Try PATH resolution (Windows/Linux)
        try {
            $where = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'where ffprobe' : 'which ffprobe';
            $pathOut = @shell_exec($where);
            if ($pathOut) {
                foreach (preg_split('/\r?\n/', trim($pathOut)) as $candidate) {
                    if ($candidate !== '') {
                        $bins[] = $candidate;
                    }
                }
            }
        } catch (\Throwable $e) { /* ignore */
        }
        // Common installs
        $bins = array_merge($bins, [
            'ffprobe',
            'C:\\ffmpeg\\bin\\ffprobe.exe',
            'C:\\Program Files\\ffmpeg\\bin\\ffprobe.exe',
            'C:\\laragon\\bin\\ffmpeg\\ffprobe.exe',
            '/usr/bin/ffprobe',
            '/usr/local/bin/ffprobe',
        ]);
        // Deduplicate
        $bins = array_values(array_unique($bins));
        foreach ($bins as $bin) {
            $cmd = sprintf('%s -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s', $bin, $arg);
            try {
                $out = @shell_exec($cmd);
                if ($out !== null) {
                    $out = trim($out);
                    if ($out !== '') {
                        $seconds = (int) round((float) $out);
                        if ($seconds > 0)
                            return $seconds;
                    }
                }
            } catch (\Throwable $e) {
                // ignore and try next
            }
        }
        return null;
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $month = trim((string) $request->get('month', '')); // YYYY-MM

        $coursesQuery = Course::query()->with([
            'category',
            'modules.quizQuestions',
            'manualPayments.user',
            'manualPayments.proofs',
        ])->withCount('enrollments')->orderByDesc('created_at');

        if ($q !== '') {
            $coursesQuery->where(function ($sub) use ($q) {
                $sub->where('name', 'like', '%' . $q . '%')
                    ->orWhereHas('category', function ($cat) use ($q) {
                        $cat->where('name', 'like', '%' . $q . '%');
                    });
            });
        }

        if ($month !== '') {
            try {
                $dt = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                $coursesQuery->whereYear('created_at', $dt->year)
                    ->whereMonth('created_at', $dt->month);
            } catch (\Throwable $e) {
                // ignore invalid month
            }
        }

        $courses = $coursesQuery->paginate(10)->withQueryString();
        return view('admin.courses.index', compact('courses'));
    }

    public function export(Request $request)
    {
        $format = (string) $request->get('format', 'pdf');
        $q = trim((string) $request->get('q', ''));
        $month = trim((string) $request->get('month', '')); // YYYY-MM

        $coursesQuery = Course::query()
            ->with(['category', 'modules'])
            ->orderByDesc('created_at');

        if ($q !== '') {
            $coursesQuery->where(function ($sub) use ($q) {
                $sub->where('name', 'like', '%' . $q . '%')
                    ->orWhereHas('category', function ($cat) use ($q) {
                        $cat->where('name', 'like', '%' . $q . '%');
                    });
            });
        }

        $periodName = 'Semua Data';
        if ($month !== '') {
            try {
                $dt = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                $coursesQuery->whereYear('created_at', $dt->year)
                    ->whereMonth('created_at', $dt->month);
                $periodName = 'Bulan ' . $dt->translatedFormat('F Y');
            } catch (\Throwable $e) {
                // ignore invalid month
            }
        }

        $courses = $coursesQuery->get();

        if ($format === 'excel') {
            return $this->exportCoursesToCsv($courses, $periodName, $q, $month);
        }

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $html = view('admin.courses.report_pdf', [
            'courses' => $courses,
            'periodName' => $periodName,
            'q' => $q,
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Daftar_Course_' . now()->format('YmdHis') . '.pdf"');
    }

    public function participants(Request $request, Course $course)
    {
        $course->loadMissing(['modules:id,course_id']);
        $totalModules = $course->modules ? $course->modules->count() : $course->modules()->count();

        $enrollments = $course->enrollments()
            ->with(['user:id,name,email', 'progress:enrollment_id,course_module_id,completed'])
            ->orderByDesc('enrolled_at')
            ->orderByDesc('created_at')
            ->get();

        $rows = $enrollments->map(function ($enr) use ($totalModules) {
            $completed = 0;
            if ($enr->relationLoaded('progress') && $enr->progress) {
                $completed = $enr->progress
                    ->where('completed', true)
                    ->unique('course_module_id')
                    ->count();
            }
            $percent = ($totalModules > 0)
                ? (int) round(($completed / $totalModules) * 100)
                : 0;

            $enrolledAt = $enr->enrolled_at ?? $enr->created_at;

            return [
                'name' => $enr->user->name ?? 'User',
                'email' => $enr->user->email ?? '-',
                'progress_percent' => $percent,
                'status' => (string) ($enr->status ?? ''),
                'status_label' => ((string) ($enr->status ?? '')) === 'active' ? 'Aktif' : ucfirst((string) ($enr->status ?? '-')),
                'enrolled_at' => $enrolledAt ? $enrolledAt->format('d-m-Y') : '-',
            ];
        })->values();

        return response()->json([
            'course_id' => $course->id,
            'total_modules' => $totalModules,
            'participants' => $rows,
        ]);
    }

    private function exportCoursesToCsv($courses, string $periodName, string $q = '', string $month = '')
    {
        $filename = 'Daftar_Course_' . now()->format('YmdHis') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($courses, $periodName, $q, $month) {
            $file = fopen('php://output', 'w');
            fputs($file, (chr(0xEF) . chr(0xBB) . chr(0xBF))); // BOM for Excel UTF-8

            fputcsv($file, ['IDSPORA - DAFTAR COURSE']);
            fputcsv($file, ['Periode:', $periodName]);
            if ($q !== '') {
                fputcsv($file, ['Filter Pencarian:', $q]);
            }
            if ($month !== '') {
                fputcsv($file, ['Filter Bulan (YYYY-MM):', $month]);
            }
            fputcsv($file, []);

            fputcsv($file, [
                'ID',
                'Nama Course',
                'Kategori',
                'Level',
                'Harga',
                'Status',
                'Status Kelengkapan',
                'Total Modul',
                'PDF',
                'Video',
                'Kuis',
                'Dibuat',
            ]);

            foreach ($courses as $course) {
                $modules = $course->modules ?? collect();
                $pdfCount = $modules->where('type', 'pdf')->count();
                $videoCount = $modules->where('type', 'video')->count();
                $quizCount = $modules->where('type', 'quiz')->count();
                $totalModules = $modules->count();

                $hasModules = $totalModules > 0;
                $isPublished = ((string) $course->status) === 'active';
                $kelengkapan = $isPublished ? 'Complete' : ($hasModules ? 'In Progress' : 'Missing Material');

                fputcsv($file, [
                    $course->id,
                    $course->name,
                    optional($course->category)->name,
                    ucfirst((string) $course->level),
                    (int) $course->price,
                    (string) $course->status,
                    $kelengkapan,
                    $totalModules,
                    $pdfCount,
                    $videoCount,
                    $quizCount,
                    $course->created_at ? $course->created_at->format('Y-m-d H:i') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(Request $request, Course $course)
    {
        $this->ensureCoursePublishedForPublic($request->user(), $course);
        $course->load('category', 'modules');
        // Students enrolled (use active enrollments for the count)
        $course->loadCount([
            'enrollments as students_count' => function ($q) {
                $q->where('status', 'active');
            },
        ]);

        $levelLabel = match ((string) $course->level) {
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
            default => ucfirst((string) $course->level),
        };

        // Temporary defaults (no columns detected yet)
        $courseLanguage = 'Indonesia';
        $certificateLabel = 'Include';

        return view('course.detail', compact('course', 'levelLabel', 'courseLanguage', 'certificateLabel'));
    }

    public function create()
    {
        $categories = Category::all();
        // Use the Tailwind-based Manage Courses create view
        return view('admin.courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'trainer_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereRaw('LOWER(role) = ?', ['trainer']);
                }),
            ],
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'status' => 'required|in:active,archive',
            'price' => 'required|integer|min:0',
            'duration' => 'required|integer|min:0',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,webm,ogg|max:204800',
            'card_thumbnail' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'discount_percent' => 'nullable|integer|min:1|max:100',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
            'free_access_mode' => 'nullable|in:all,limit_2',
            'expenses' => 'nullable|array',
            'expenses.*.item' => 'nullable|string|max:255',
            'expenses.*.quantity' => 'nullable|integer|min:0',
            'expenses.*.unit_price' => 'nullable|integer|min:0',
            'expenses.*.total' => 'nullable|integer|min:0',
            'module_files' => 'sometimes|array',
            'module_files.*' => 'file|mimes:pdf,mp4,webm,ogg|max:204800'
        ]);

        $expensesInput = $request->input('expenses');
        $expensesJson = null;
        if (is_array($expensesInput)) {
            $normalized = [];
            foreach ($expensesInput as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $item = trim((string)($row['item'] ?? ''));
                $qty = (int)($row['quantity'] ?? 0);
                $unit = (int)($row['unit_price'] ?? 0);
                if ($item === '' && $qty === 0 && $unit === 0) {
                    continue;
                }
                $qty = max(0, $qty);
                $unit = max(0, $unit);
                $total = max(0, $qty * $unit);
                $normalized[] = [
                    'item' => $item,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'total' => $total,
                ];
            }
            $expensesJson = !empty($normalized) ? $normalized : null;
        }

        // Handle media upload (image or video)
        $mediaFile = $request->file('image');
        $mediaPath = $mediaFile->store('courses', 'public');
        $mediaType = str_starts_with($mediaFile->getMimeType(), 'video/') ? 'video' : 'image';

        // Handle card thumbnail upload
        $cardThumbPath = null;
        if ($request->hasFile('card_thumbnail')) {
            $cardThumbPath = $request->file('card_thumbnail')->store('courses/card_thumbnails', 'public');
        }

        // Create course
        $course = Course::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'trainer_id' => $request->input('trainer_id') ?: null,
            'description' => $this->sanitizeDescription($request->description),
            'level' => $request->level,
            'status' => $request->status,
            'price' => $request->price,
            'free_access_mode' => $request->input('free_access_mode', 'limit_2'),
            'duration' => $request->duration,
            'media' => $mediaPath,
            'media_type' => $mediaType,
            'card_thumbnail' => $cardThumbPath,
            'discount_percent' => $request->discount_percent,
            'discount_start' => $request->discount_start,
            'discount_end' => $request->discount_end,
            'expenses_json' => $expensesJson,
        ]);

        if (!empty($course->trainer_id)) {
            $this->notifyTrainerCourseInvitation($course, (int) $course->trainer_id);
        }

        // Create modules from payload
        $modulesPayload = json_decode($request->input('modules_payload', '[]'), true);
        if (is_array($modulesPayload) && !empty($modulesPayload)) {
            $uploaded = $request->file('module_files', []);
            foreach ($modulesPayload as $idx => $m) {
                $title = is_string($m['title'] ?? null) ? $m['title'] : ('Module ' . ($idx + 1));
                $type = in_array(($m['type'] ?? 'video'), ['video', 'pdf', 'quiz']) ? $m['type'] : 'video';
                $order = (int) ($m['order'] ?? ($idx + 1));
                $desc = is_string($m['subtitle'] ?? null) ? $m['subtitle'] : null;
                $filename = is_string($m['filename'] ?? null) ? $m['filename'] : (Str::slug($title) . '.dat');
                $contentUrl = 'uploads/modules/' . $filename;
                $uid = $m['uid'] ?? null;
                $fileNameMeta = $filename;
                $mimeMeta = null;
                $sizeMeta = 0;
                $durationSeconds = 0;

                if ($uid && is_array($uploaded) && array_key_exists($uid, $uploaded) && $uploaded[$uid]) {
                    $file = $uploaded[$uid];
                    $storedPath = $file->store("courses/{$course->id}/modules", 'public');
                    if ($storedPath) {
                        $contentUrl = $storedPath;
                    }
                    $fileNameMeta = $file->getClientOriginalName() ?: $fileNameMeta;
                    $mimeMeta = $file->getMimeType();
                    $sizeMeta = (int) $file->getSize();

                    if ($type === 'video') {
                        $abs = Storage::disk('public')->path($storedPath);
                        $dur = $this->probeVideoDurationSeconds($abs);
                        if (is_int($dur) && $dur > 0) {
                            $durationSeconds = $dur;
                        }
                    }
                }
                $currMod = CourseModule::create([
                    'course_id' => $course->id,
                    'order_no' => $order,
                    'title' => $title,
                    'description' => $desc,
                    'type' => $type,
                    'content_url' => $contentUrl,
                    'file_name' => $fileNameMeta,
                    'mime_type' => $mimeMeta,
                    'file_size' => $sizeMeta,
                    'is_free' => false,
                    'preview_pages' => 0,
                    'duration' => $durationSeconds,
                ]);

                // Save Quiz Data
                if ($type === 'quiz' && isset($m['data']) && is_array($m['data'])) {
                    $quizData = $m['data'];
                    if (isset($quizData['questions']) && is_array($quizData['questions'])) {
                        foreach ($quizData['questions'] as $qIdx => $q) {
                            $questionText = $q['text'] ?? '';
                            if (!empty($questionText)) {
                                $quizQ = QuizQuestion::create([
                                    'course_module_id' => $currMod->id,
                                    'question' => $questionText,
                                    'explanation' => '',
                                    'order_no' => $qIdx + 1,
                                    'points' => 10,
                                ]);

                                $options = $q['options'] ?? [];
                                $correctIdx = $q['correctIndex'] ?? -1;
                                foreach ($options as $oIdx => $oText) {
                                    if (trim($oText) === '')
                                        continue;
                                    QuizAnswer::create([
                                        'quiz_question_id' => $quizQ->id,
                                        'answer_text' => $oText,
                                        'is_correct' => ($oIdx == $correctIdx),
                                        'order_no' => $oIdx + 1,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully!');
    }

    public function edit(Course $course)
    {
        $categories = Category::all();
        $course->load('modules.quizQuestions.answers');
        return view('admin.courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        $previousTrainerId = (int) ($course->trainer_id ?? 0);

        $delIdsRaw = $request->input('modules_delete_ids');
        if (is_array($delIdsRaw)) {
            $request->merge(['modules_delete_ids' => implode(',', $delIdsRaw)]);
        }
        $payloadNewRaw = $request->input('modules_payload_new');
        if (is_array($payloadNewRaw)) {
            $request->merge(['modules_payload_new' => json_encode($payloadNewRaw)]);
        }

        $orderUpdatesRaw = $request->input('modules_order_updates');
        if (is_array($orderUpdatesRaw)) {
            $request->merge(['modules_order_updates' => json_encode($orderUpdatesRaw)]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'trainer_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereRaw('LOWER(role) = ?', ['trainer']);
                }),
            ],
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'status' => 'required|in:active,archive',
            'price' => 'required|integer|min:0',
            'duration' => 'required|integer|min:0',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,webm,ogg|max:204800',
            // Legacy single-upload (kept for backward compatibility)
            'admin_video_file' => 'nullable|file|mimes:mp4,webm,ogg|max:204800',
            'admin_video_title' => 'nullable|string|max:255',
            'admin_video_description' => 'nullable|string',
            // New multi-upload list
            'admin_videos' => 'nullable|array',
            'admin_videos.*.title' => 'nullable|string|max:255',
            'admin_videos.*.description' => 'nullable|string',
            'admin_videos.*.order_no' => 'nullable|integer|min:1',
            'admin_videos.*.file' => 'nullable|file|mimes:mp4,webm,ogg|max:204800',
            'modules_delete_ids' => 'nullable|string',
            'modules_payload_new' => 'nullable|string',
            'modules_order_updates' => 'nullable|string',
            'free_access_mode' => 'nullable|in:all,limit_2',
            'expenses' => 'nullable|array',
            'expenses.*.item' => 'nullable|string|max:255',
            'expenses.*.quantity' => 'nullable|integer|min:0',
            'expenses.*.unit_price' => 'nullable|integer|min:0',
            'expenses.*.total' => 'nullable|integer|min:0',
            'module_files' => 'sometimes|array',
            'module_files.*' => 'file|mimes:pdf,mp4,webm,ogg|max:204800'
        ]);

        $data = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $this->sanitizeDescription($request->description),
            'level' => $request->level,
            'status' => $request->status,
            'price' => $request->price,
            'free_access_mode' => $request->input('free_access_mode', $course->free_access_mode ?? 'limit_2'),
            'duration' => $request->duration,
            'discount_percent' => $request->discount_percent,
            'discount_start' => $request->discount_start,
            'discount_end' => $request->discount_end,
        ];

        if ($request->hasFile('card_thumbnail')) {
            if ($course->card_thumbnail && Storage::disk('public')->exists($course->card_thumbnail)) {
                Storage::disk('public')->delete($course->card_thumbnail);
            }
            $cardThumbPath = $request->file('card_thumbnail')->store('courses/card_thumbnails', 'public');
            $data['card_thumbnail'] = $cardThumbPath;
        }

        if ($request->hasFile('image')) {
            if ($course->media && Storage::disk('public')->exists($course->media)) {
                Storage::disk('public')->delete($course->media);
            }
            $mediaFile = $request->file('image');
            $mediaPath = $mediaFile->store('courses', 'public');
            $mediaType = str_starts_with($mediaFile->getMimeType(), 'video/') ? 'video' : 'image';
            $data['media'] = $mediaPath;
            $data['media_type'] = $mediaType;
        }

        $course->update($data);

        $currentTrainerId = (int) ($course->trainer_id ?? 0);
        if ($currentTrainerId > 0 && $currentTrainerId !== $previousTrainerId) {
            $this->notifyTrainerCourseInvitation($course, $currentTrainerId);
        }

        $deleteIdsCsv = (string) $request->input('modules_delete_ids', '');
        $deleteIds = collect(preg_split('/[,\s]+/', trim($deleteIdsCsv)))->filter()->map(fn($v) => (int) $v)->all();
        if (!empty($deleteIds)) {
            $modulesToDelete = CourseModule::where('course_id', $course->id)->whereIn('id', $deleteIds)->get();
            foreach ($modulesToDelete as $mod) {
                if ($mod->content_url && Storage::disk('public')->exists($mod->content_url)) {
                    Storage::disk('public')->delete($mod->content_url);
                }
                $mod->delete();
            }
        }

        $orderUpdatesInput = $request->input('modules_order_updates', '{}');
        $orderUpdates = is_array($orderUpdatesInput) ? $orderUpdatesInput : json_decode((string) $orderUpdatesInput, true);
        if (is_array($orderUpdates) && !empty($orderUpdates)) {
            foreach ($orderUpdates as $moduleId => $orderNo) {
                $moduleId = (int) $moduleId;
                $orderNo = (int) $orderNo;
                if ($moduleId <= 0 || $orderNo <= 0) {
                    continue;
                }
                CourseModule::where('course_id', $course->id)
                    ->where('id', $moduleId)
                    ->update(['order_no' => $orderNo]);
            }
        }

        $modulesPayloadInput = $request->input('modules_payload_new', '[]');
        $modulesPayload = is_array($modulesPayloadInput) ? $modulesPayloadInput : json_decode($modulesPayloadInput, true);

        if (is_array($modulesPayload) && !empty($modulesPayload)) {
            $uploaded = $request->file('module_files', []);
            foreach ($modulesPayload as $idx => $m) {
                $title = is_string($m['title'] ?? null) ? $m['title'] : ('Module ' . ($idx + 1));
                $type = in_array(($m['type'] ?? 'video'), ['video', 'pdf', 'quiz']) ? $m['type'] : 'video';
                $order = (int) ($m['order'] ?? ($idx + 1));
                $desc = is_string($m['subtitle'] ?? null) ? $m['subtitle'] : null;
                $filename = is_string($m['filename'] ?? null) ? $m['filename'] : (Str::slug($title) . '.dat');
                $contentUrl = 'uploads/modules/' . $filename;
                $uid = $m['uid'] ?? null;
                $fileNameMeta = $filename;
                $mimeMeta = null;
                $sizeMeta = 0;
                $durationSeconds = 0;

                if ($uid && is_array($uploaded) && array_key_exists($uid, $uploaded) && $uploaded[$uid]) {
                    $file = $uploaded[$uid];
                    $storedPath = $file->store("courses/{$course->id}/modules", 'public');
                    if ($storedPath) {
                        $contentUrl = $storedPath;
                    }
                    $fileNameMeta = $file->getClientOriginalName() ?: $fileNameMeta;
                    $mimeMeta = $file->getMimeType();
                    $sizeMeta = (int) $file->getSize();
                    if ($type === 'video') {
                        $abs = Storage::disk('public')->path($storedPath);
                        $dur = $this->probeVideoDurationSeconds($abs);
                        if (is_int($dur) && $dur > 0) {
                            $durationSeconds = $dur;
                        }
                    }
                }
                $currMod = CourseModule::create([
                    'course_id' => $course->id,
                    'order_no' => $order,
                    'title' => $title,
                    'description' => $desc,
                    'type' => $type,
                    'content_url' => $contentUrl,
                    'file_name' => $fileNameMeta,
                    'mime_type' => $mimeMeta,
                    'file_size' => $sizeMeta,
                    'is_free' => false,
                    'preview_pages' => 0,
                    'duration' => $durationSeconds,
                ]);

                // Save Quiz Data (Update)
                if ($type === 'quiz' && isset($m['data']) && is_array($m['data'])) {
                    $quizData = $m['data'];
                    if (isset($quizData['questions']) && is_array($quizData['questions'])) {
                        foreach ($quizData['questions'] as $qIdx => $q) {
                            $questionText = $q['text'] ?? '';
                            if (!empty($questionText)) {
                                $quizQ = QuizQuestion::create([
                                    'course_module_id' => $currMod->id,
                                    'question' => $questionText,
                                    'explanation' => '',
                                    'order_no' => $qIdx + 1,
                                    'points' => 10,
                                ]);

                                $options = $q['options'] ?? [];
                                $correctIdx = $q['correctIndex'] ?? -1;
                                foreach ($options as $oIdx => $oText) {
                                    if (trim($oText) === '')
                                        continue;
                                    QuizAnswer::create([
                                        'quiz_question_id' => $quizQ->id,
                                        'answer_text' => $oText,
                                        'is_correct' => ($oIdx == $correctIdx),
                                        'order_no' => $oIdx + 1,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }

        // Admin: upload multiple videos directly from Edit Course (creates new video modules)
        $adminVideosInput = $request->input('admin_videos');
        if (is_array($adminVideosInput) && !empty($adminVideosInput)) {
            $usedOrders = CourseModule::where('course_id', $course->id)->pluck('order_no')->map(fn($v) => (int) $v)->all();
            $usedOrdersSet = array_fill_keys($usedOrders, true);
            $maxOrder = !empty($usedOrders) ? max($usedOrders) : 0;

            foreach ($adminVideosInput as $i => $row) {
                if (!is_array($row)) {
                    continue;
                }

                $file = $request->file("admin_videos.$i.file");
                if (!$file) {
                    continue;
                }

                $storedPath = $file->store("courses/{$course->id}/modules", 'public');
                $fileNameMeta = $file->getClientOriginalName() ?: 'admin-video';
                $mimeMeta = $file->getMimeType();
                $sizeMeta = (int) $file->getSize();

                $titleInput = trim((string) ($row['title'] ?? ''));
                $title = $titleInput !== '' ? $titleInput : (pathinfo($fileNameMeta, PATHINFO_FILENAME) ?: 'Admin Video');
                $desc = isset($row['description']) && trim((string) $row['description']) !== '' ? (string) $row['description'] : null;

                $requestedOrder = (int) ($row['order_no'] ?? 0);
                $orderNo = 0;
                if ($requestedOrder >= 1 && empty($usedOrdersSet[$requestedOrder])) {
                    $orderNo = $requestedOrder;
                } else {
                    $orderNo = $maxOrder + 1;
                }
                $usedOrdersSet[$orderNo] = true;
                $maxOrder = max($maxOrder, $orderNo);

                $durationSeconds = 0;
                try {
                    $abs = Storage::disk('public')->path($storedPath);
                    $dur = $this->probeVideoDurationSeconds($abs);
                    if (is_int($dur) && $dur > 0) {
                        $durationSeconds = $dur;
                    }
                } catch (\Throwable $e) {
                    // ignore duration probing failures
                }

                CourseModule::create([
                    'course_id' => $course->id,
                    'order_no' => $orderNo,
                    'title' => $title,
                    'description' => $desc,
                    'type' => 'video',
                    'content_url' => $storedPath,
                    'file_name' => $fileNameMeta,
                    'mime_type' => $mimeMeta,
                    'file_size' => $sizeMeta,
                    'is_free' => false,
                    'preview_pages' => 0,
                    'duration' => $durationSeconds,
                ]);
            }
        }

        // Admin: upload video directly from Edit Course (creates a new video module)
        if ($request->hasFile('admin_video_file')) {
            $file = $request->file('admin_video_file');
            $storedPath = $file->store("courses/{$course->id}/modules", 'public');
            $fileNameMeta = $file->getClientOriginalName() ?: 'admin-video';
            $mimeMeta = $file->getMimeType();
            $sizeMeta = (int) $file->getSize();
            $titleInput = trim((string) $request->input('admin_video_title', ''));
            $title = $titleInput !== '' ? $titleInput : (pathinfo($fileNameMeta, PATHINFO_FILENAME) ?: 'Admin Video');
            $desc = $request->filled('admin_video_description') ? (string) $request->input('admin_video_description') : null;

            $durationSeconds = 0;
            try {
                $abs = Storage::disk('public')->path($storedPath);
                $dur = $this->probeVideoDurationSeconds($abs);
                if (is_int($dur) && $dur > 0) {
                    $durationSeconds = $dur;
                }
            } catch (\Throwable $e) {
                // ignore duration probing failures
            }

            $nextOrder = (int) (CourseModule::where('course_id', $course->id)->max('order_no') ?? 0) + 1;
            CourseModule::create([
                'course_id' => $course->id,
                'order_no' => $nextOrder,
                'title' => $title,
                'description' => $desc,
                'type' => 'video',
                'content_url' => $storedPath,
                'file_name' => $fileNameMeta,
                'mime_type' => $mimeMeta,
                'file_size' => $sizeMeta,
                'is_free' => false,
                'preview_pages' => 0,
                'duration' => $durationSeconds,
            ]);
        }

        return redirect()->route('admin.courses.index')->with('success', 'Course berhasil diedit!');
    }

    public function destroy(Course $course)
    {
        if ($course->media && Storage::disk('public')->exists($course->media)) {
            Storage::disk('public')->delete($course->media);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully!');
    }
}