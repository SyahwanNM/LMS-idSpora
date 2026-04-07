<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Quiz;
use App\Models\TrainerNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\TrainerCertificate;
use App\Services\CourseTemplateCloneService;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Str;

class TrainerController extends Controller
{
    private function ensureTrainerCertificatesSynced($trainer): void
    {
        $trainerId = (int) ($trainer->id ?? 0);
        if ($trainerId <= 0) {
            return;
        }

        $finishedEvents = Event::query()
            ->where('trainer_id', $trainerId)
            ->whereNotNull('event_date')
            ->whereDate('event_date', '<', now()->toDateString())
            ->get(['id', 'title', 'event_date', 'jenis', 'certificate_logo', 'certificate_signature']);

        $finishedCourses = Course::query()
            ->where('trainer_id', $trainerId)
            ->where('status', 'approved')
            ->whereNotNull('approved_at')
            ->where('approved_at', '<', now())
            ->get(['id', 'name', 'approved_at']);

        $existing = TrainerCertificate::query()
            ->where('trainer_id', $trainerId)
            ->where('status', 'sent')
            ->where(function ($q) {
                $q->where('certifiable_type', Event::class)
                    ->orWhere('certifiable_type', Course::class);
            })
            ->get(['certifiable_type', 'certifiable_id'])
            ->mapWithKeys(function ($cert) {
                return [((string) $cert->certifiable_type) . ':' . (int) $cert->certifiable_id => true];
            });

        foreach ($finishedEvents as $event) {
            $key = Event::class . ':' . (int) $event->id;
            if (!isset($existing[$key])) {
                $this->issueAutoTrainerCertificate($trainer, $event, 'event');
                $existing[$key] = true;
            }
        }

        foreach ($finishedCourses as $course) {
            $key = Course::class . ':' . (int) $course->id;
            if (!isset($existing[$key])) {
                $this->issueAutoTrainerCertificate($trainer, $course, 'course');
                $existing[$key] = true;
            }
        }
    }

    private function issueAutoTrainerCertificate($trainer, $certifiable, string $context): void
    {
        $trainerId = (int) ($trainer->id ?? 0);
        if ($trainerId <= 0) {
            return;
        }

        $certifiableType = $context === 'event' ? Event::class : Course::class;
        $certifiableId = (int) ($certifiable->id ?? 0);
        if ($certifiableId <= 0) {
            return;
        }

        $alreadyExists = TrainerCertificate::query()
            ->where('trainer_id', $trainerId)
            ->where('status', 'sent')
            ->where('certifiable_type', $certifiableType)
            ->where('certifiable_id', $certifiableId)
            ->exists();

        if ($alreadyExists) {
            return;
        }

        $issuedAt = now();
        if ($context === 'event' && !empty($certifiable->event_date)) {
            $issuedAt = Carbon::parse($certifiable->event_date)->endOfDay();
        } elseif ($context === 'course' && !empty($certifiable->approved_at)) {
            $issuedAt = Carbon::parse($certifiable->approved_at);
        }

        $activityCodeMap = [
            'webinar' => 'WBN',
            'seminar' => 'SMN',
            'workshop' => 'WRT',
            'training' => 'WRT',
            'video' => 'VDP',
            'e-learning' => 'ELR',
            'elearning' => 'ELR',
        ];

        $activityCode = $context === 'event'
            ? ($activityCodeMap[strtolower((string) ($certifiable->jenis ?? ''))] ?? 'WBN')
            : 'ELR';
        $typeCode = 'TRN';

        // Get the maximum sequence for this period to avoid duplicates
        $monthYear = $issuedAt->format('m-Y');
        $maxSequence = TrainerCertificate::query()
            ->where('trainer_id', $trainerId)
            ->where('activity_code', strtoupper($activityCode))
            ->where('type_code', $typeCode)
            ->whereRaw("date_format(issued_at, '%m-%Y') = ?", [$monthYear])
            ->max(\DB::raw("CAST(SUBSTRING(sequence, -3) AS UNSIGNED)")) ?? 0;

        $sequenceNum = $maxSequence + 1;
        $sequence = str_pad((string) $sequenceNum, 3, '0', STR_PAD_LEFT);

        $certificateNumber = $this->buildIdsporaCertificateNumber($activityCode, $typeCode, $sequence, $issuedAt);

        // Check if certificate already exists - skip if it does
        $existingCert = TrainerCertificate::where('certificate_number', $certificateNumber)->first();
        if ($existingCert) {
            return;
        }

        $trainerCertificate = TrainerCertificate::create([
            'trainer_id' => $trainerId,
            'certifiable_type' => $certifiableType,
            'certifiable_id' => $certifiableId,
            'activity_code' => strtoupper($activityCode),
            'type_code' => $typeCode,
            'sequence' => $sequence,
            'certificate_number' => $certificateNumber,
            'issued_at' => $issuedAt,
            'issued_by' => null,
            'status' => 'sent',
        ]);

        $logosBase64 = [];
        $signaturesBase64 = [];
        if ($context === 'event' && $certifiable instanceof Event) {
            [$logosBase64, $signaturesBase64] = $this->extractEventAssetsBase64($certifiable);
        }

        $pdfData = [
            'context' => $context,
            'event' => $context === 'event' ? $certifiable : null,
            'course' => $context === 'course' ? $certifiable : null,
            'user' => $trainer,
            'issuedAt' => $issuedAt,
            'certificateNumber' => $certificateNumber,
            'logosBase64' => $logosBase64,
            'signaturesBase64' => $signaturesBase64,
            'roleLabel' => $this->certificateTypeLabel($typeCode),
        ];

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);
        $dompdf->setOptions($options);
        $html = view('trainer.certificates.certificate-pdf', $pdfData)->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $relativeDir = 'trainer_certificates/' . $trainerId . '/' . $context . '/' . $certifiableId;
        $filename = Str::slug($certificateNumber, '_') . '.pdf';
        $relativePath = $relativeDir . '/' . $filename;
        $absolutePath = storage_path('app/' . $relativePath);

        if (!is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }
        file_put_contents($absolutePath, $dompdf->output());
        $trainerCertificate->update(['file_path' => $relativePath]);

        $contextLabel = $context === 'event' ? 'event' : 'course';
        $contextTitle = $context === 'event'
            ? (string) ($certifiable->title ?? '')
            : (string) ($certifiable->name ?? '');

        TrainerNotification::create([
            'trainer_id' => $trainerId,
            'type' => 'certificate_issued',
            'title' => 'Sertifikat otomatis diterbitkan',
            'message' => 'Sertifikat untuk ' . $contextLabel . ($contextTitle !== '' ? ' "' . $contextTitle . '"' : '') . ' sudah tersedia. No: ' . $certificateNumber,
            'data' => [
                'entity_type' => $context,
                'entity_id' => $certifiableId,
                'certificate_number' => $certificateNumber,
                'url' => route('trainer.certificates.index') . '?context=' . $context . '&id=' . $certifiableId,
            ],
            'expires_at' => now()->addDays(30),
        ]);
    }

    private function mapStudioMaterialModule(int $courseId, \App\Models\CourseModule $module): array
    {
        return [
            'module_id' => (int) $module->id,
            'order_no' => (int) $module->order_no,
            'type' => (string) $module->type,
            'title' => (string) ($module->title ?? ''),
            'file_name' => (string) ($module->file_name ?: basename((string) $module->content_url)),
            'view_url' => route('trainer.courses.studio.material.view', [$courseId, $module->id]),
            'updated_at' => optional($module->updated_at)->toDateTimeString(),
        ];
    }

    private function ensureTemplateStructureExists(Course $course): void
    {
        if ((int) ($course->template_id ?? 0) <= 0) {
            return;
        }

        if ((int) $course->modules()->count() > 0) {
            return;
        }

        $template = $course->template()->with('modules')->first();
        if (!$template || $template->modules->isEmpty()) {
            return;
        }

        app(CourseTemplateCloneService::class)
            ->cloneToCourse($course, $template, replaceExisting: false);
    }

    private function ensureQuizSlotPerUnit(Course $course): void
    {
        $modules = CourseModule::where('course_id', $course->id)
            ->with(['quizQuestions.answers'])
            ->orderBy('order_no', 'asc')
            ->get();

        if ($modules->isEmpty()) {
            return;
        }

        $unitChunks = $modules->chunk(3)->values();

        foreach ($unitChunks as $chunk) {
            $chunk = $chunk->values();
            $expectedTypes = ['pdf', 'video', 'quiz'];

            foreach ($expectedTypes as $index => $expectedType) {
                $slotModule = $chunk->get($index);
                if (!($slotModule instanceof CourseModule)) {
                    continue;
                }

                $currentType = (string) $slotModule->type;
                if ($currentType === $expectedType) {
                    continue;
                }

                $updates = ['type' => $expectedType];

                if ($expectedType === 'quiz') {
                    // Quiz slot must not keep material file payload.
                    $updates['content_url'] = '';
                    $updates['file_name'] = null;
                    $updates['mime_type'] = null;
                    $updates['file_size'] = 0;
                }

                $slotModule->update($updates);

                if ($currentType === 'quiz' && $expectedType !== 'quiz') {
                    // Old quiz slot is converted to material slot; remove stale quiz content.
                    $slotModule->quizQuestions()->delete();
                }
            }
        }
    }

    /**
     * Show trainer profile with their courses
     */
    public function dashboard()
    {
        $user = Auth::user();
        $this->ensureTrainerCertificatesSynced($user);

        $coursesQuery = $user->coursesAsTrainer();

        $priorityCourses = (clone $coursesQuery)
            ->withCount([
                'enrollments' => function ($query) {
                    $query->where('enrollments.status', 'active');
                }
            ])
            ->orderByRaw("CASE
                WHEN status IN ('archive', 'archived', 'draft') THEN 0
                WHEN status IN ('published', 'approved', 'active') THEN 1
                ELSE 2
            END")
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        $totalCourses = (clone $coursesQuery)->count();
        $attentionCourseCount = (clone $coursesQuery)
            ->whereIn('status', ['archive', 'archived', 'draft'])
            ->count();

        $activeCourseCount = (clone $coursesQuery)
            ->whereIn('status', ['published', 'approved', 'active'])
            ->count();

        $activeEventsQuery = Event::query()
            ->where('trainer_id', $user->id)
            ->whereNotNull('event_date')
            ->whereRaw("TIMESTAMP(event_date, COALESCE(event_time_end, COALESCE(event_time, '23:59:59'))) >= ?", [now()->format('Y-m-d H:i:s')]);

        $priorityEvents = (clone $activeEventsQuery)
            ->withCount([
                'registrations as participants_count' => function ($q) {
                    $q->where('status', 'active');
                }
            ])
            ->orderBy('event_date', 'asc')
            ->orderBy('event_time', 'asc')
            ->limit(4)
            ->get();

        $activeEventCount = (clone $activeEventsQuery)->count();

        $todoMaterials = (clone $activeEventsQuery)
            ->where(function ($q) {
                $q->whereNull('module_path')
                    ->orWhere('module_path', '');
            })
            ->orderBy('event_date', 'asc')
            ->orderBy('event_time', 'asc')
            ->limit(4)
            ->get();

        $students = $user->trainerEnrollments()
            ->with(['student', 'course'])
            ->orderBy('enrollments.created_at', 'desc')
            ->limit(5)
            ->get();

        $totalStudents = $user->trainerEnrollments()
            ->where('enrollments.status', 'active')
            ->distinct('user_id')
            ->count('user_id');

        $dashboardInvitations = TrainerNotification::query()
            ->where('trainer_id', $user->id)
            ->whereIn('type', ['course_invitation', 'event_invitation'])
            ->orderByRaw('CASE WHEN read_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('created_at')
            ->limit(40)
            ->get()
            ->filter(function ($notification) {
                $statusFromData = trim((string) data_get($notification->data, 'invitation_status', ''));
                $statusFromColumn = trim((string) ($notification->invitation_status ?? ''));
                $effectiveStatus = $statusFromData !== '' ? $statusFromData : ($statusFromColumn !== '' ? $statusFromColumn : 'pending');
                return $effectiveStatus === 'pending';
            })
            ->take(5)
            ->values();

        $todoConfirmations = TrainerNotification::query()
            ->where('trainer_id', $user->id)
            ->whereIn('type', ['course_invitation', 'event_invitation'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->filter(function ($notification) {
                $status = (string) data_get($notification->data, 'invitation_status', 'pending');
                return $status === 'pending';
            })
            ->take(4)
            ->values();

        $unreadInvitationCount = TrainerNotification::query()
            ->where('trainer_id', $user->id)
            ->whereIn('type', ['course_invitation', 'event_invitation'])
            ->whereNull('read_at')
            ->get()
            ->filter(function ($notification) {
                $statusFromData = trim((string) data_get($notification->data, 'invitation_status', ''));
                $statusFromColumn = trim((string) ($notification->invitation_status ?? ''));
                $effectiveStatus = $statusFromData !== '' ? $statusFromData : ($statusFromColumn !== '' ? $statusFromColumn : 'pending');
                return $effectiveStatus === 'pending';
            })
            ->count();

        $teachingHistory = TrainerCertificate::query()
            ->where('trainer_id', $user->id)
            ->where('status', 'sent')
            ->whereNotNull('file_path')
            ->with('certifiable')
            ->latest('issued_at')
            ->limit(6)
            ->get();

        $totalCertificates = (clone TrainerCertificate::query())
            ->where('trainer_id', $user->id)
            ->where('status', 'sent')
            ->whereNotNull('file_path')
            ->count();

        return view('trainer.dashboard', compact(
            'priorityCourses',
            'priorityEvents',
            'todoMaterials',
            'todoConfirmations',
            'students',
            'totalCourses',
            'attentionCourseCount',
            'activeCourseCount',
            'activeEventCount',
            'totalStudents',
            'totalCertificates',
            'teachingHistory',
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
            ->get();

        $certifiedCourseIds = \App\Models\TrainerCertificate::query()
            ->where('trainer_id', $user->id)
            ->where('status', 'sent')
            ->where('certifiable_type', \App\Models\Course::class)
            ->whereNotNull('file_path')
            ->pluck('certifiable_id')
            ->map(fn($id) => (int) $id)
            ->all();

        $certifiedCourseIdSet = array_fill_keys($certifiedCourseIds, true);

        $pendingInvitationCourseIds = TrainerNotification::query()
            ->where('trainer_id', $user->id)
            ->where('type', 'course_invitation')
            ->where('invitation_status', 'pending')
            ->get(['data'])
            ->map(function (TrainerNotification $notification) {
                return (int) data_get($notification->data, 'entity_id', 0);
            })
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $pendingInvitationCourseIdSet = array_fill_keys($pendingInvitationCourseIds, true);

        $finishedCourses = $courses->filter(function ($course) use ($certifiedCourseIdSet) {
            $status = (string) ($course->status ?? '');
            return isset($certifiedCourseIdSet[(int) $course->id])
                || in_array($status, ['completed', 'finished', 'archived'], true);
        })->values();

        $ongoingCourses = $courses->filter(function ($course) use ($certifiedCourseIdSet, $pendingInvitationCourseIdSet) {
            $status = (string) ($course->status ?? '');
            return !isset($certifiedCourseIdSet[(int) $course->id])
                && !isset($pendingInvitationCourseIdSet[(int) $course->id])
                && in_array($status, ['approved', 'published', 'active'], true);
        })->values();

        $upcomingCourses = $courses->filter(function ($course) use ($finishedCourses, $ongoingCourses, $pendingInvitationCourseIdSet) {
            return !$finishedCourses->contains('id', $course->id)
                && (isset($pendingInvitationCourseIdSet[(int) $course->id]) || !$ongoingCourses->contains('id', $course->id));
        })->values();

        return view('trainer.courses', compact(
            'courses',
            'certifiedCourseIdSet',
            'ongoingCourses',
            'upcomingCourses',
            'finishedCourses'
        ));
    }

    public function courseDetail($id)
    {
        $trainerId = \Illuminate\Support\Facades\Auth::id();

        $baseCourse = \App\Models\Course::query()
            ->where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        // Safety net: if admin forgot to clone template slots, clone on-demand for trainer UI.
        $this->ensureTemplateStructureExists($baseCourse);

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
                },
                'scheduleItems as schedule_count'
            ])
            ->withAvg('feedbacks', 'rating');

        if ($search) {
            $query->where('title', 'LIKE', "%{$search}%");
        }

        $events = $query
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->get();

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

        $now = now();
        $finishedEvents = collect();
        $ongoingEvents = collect();
        $upcomingEvents = collect();

        foreach ($events as $event) {
            $date = $event->event_date instanceof Carbon
                ? $event->event_date->copy()
                : Carbon::parse((string) $event->event_date);

            $timeStart = !empty($event->event_time)
                ? Carbon::parse((string) $event->event_time)->format('H:i:s')
                : '00:00:00';

            $startAt = Carbon::parse($date->format('Y-m-d') . ' ' . $timeStart);

            $timeEnd = !empty($event->event_time_end)
                ? Carbon::parse((string) $event->event_time_end)->format('H:i:s')
                : '23:59:59';

            $endAt = Carbon::parse($date->format('Y-m-d') . ' ' . $timeEnd);
            if ($endAt->lt($startAt)) {
                $endAt = $startAt->copy()->endOfDay();
            }

            if ($now->gt($endAt) || isset($certifiedEventIdSet[(int) $event->id])) {
                $finishedEvents->push($event);
            } elseif ($now->lt($startAt)) {
                $upcomingEvents->push($event);
            } else {
                $ongoingEvents->push($event);
            }
        }

        return view('trainer.events', compact(
            'events',
            'upcomingCount',
            'search',
            'certifiedEventIdSet',
            'ongoingEvents',
            'upcomingEvents',
            'finishedEvents'
        ));
    }

    public function eventDetail($id)
    {
        $trainerId = \Illuminate\Support\Facades\Auth::id();

        $event = \App\Models\Event::where('id', $id)
            ->where('trainer_id', $trainerId)
            ->with([
                'scheduleItems' => function ($q) {
                    $q->orderBy('start', 'asc');
                }
            ])
            ->firstOrFail();

        return view('trainer.detail-event', compact('event'));
    }

    public function downloadEventVbg($id)
    {
        $trainerId = \Illuminate\Support\Facades\Auth::id();

        $event = \App\Models\Event::where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        $isOfflineEvent = !empty($event->maps_url)
            || (!empty($event->latitude) && !empty($event->longitude));

        // Primary source: vbg_path. Legacy fallback for old online events: image.
        $candidateSources = [];
        if (!empty($event->vbg_path)) {
            $candidateSources[] = (string) $event->vbg_path;
        }
        if (empty($event->vbg_path) && !$isOfflineEvent && !empty($event->image)) {
            $candidateSources[] = (string) $event->image;
        }

        if (empty($candidateSources)) {
            abort(404, 'VBG file tidak tersedia untuk event ini.');
        }

        $filePath = null;
        $externalUrlFallback = null;

        foreach ($candidateSources as $sourcePath) {
            $sourcePath = trim($sourcePath);
            if ($sourcePath === '') {
                continue;
            }

            $normalized = $sourcePath;
            if (preg_match('#^https?://#i', $sourcePath)) {
                $externalUrlFallback = $sourcePath;
                $urlPath = parse_url($sourcePath, PHP_URL_PATH);
                if (!is_string($urlPath) || trim($urlPath) === '') {
                    continue;
                }
                $normalized = $urlPath;
            }

            $normalized = str_replace('\\', '/', (string) $normalized);
            $normalized = preg_replace('#^\./#', '', $normalized) ?? $normalized;
            $normalized = ltrim($normalized, '/');

            // Common legacy prefixes
            if (str_starts_with($normalized, 'public/')) {
                $normalized = ltrim(substr($normalized, 7), '/');
            }
            if (str_starts_with($normalized, 'storage/app/public/')) {
                $normalized = ltrim(substr($normalized, 19), '/');
            }
            if (str_starts_with($normalized, 'storage/')) {
                $normalized = ltrim(substr($normalized, 8), '/');
            }

            $possiblePaths = [
                storage_path('app/public/' . $normalized),
                public_path('uploads/' . $normalized),
                public_path($normalized),
            ];

            if (str_starts_with($normalized, 'uploads/')) {
                $possiblePaths[] = storage_path('app/public/' . ltrim(substr($normalized, 8), '/'));
            }

            $possiblePaths = array_values(array_unique($possiblePaths));
            foreach ($possiblePaths as $path) {
                if (file_exists($path) && is_file($path)) {
                    $filePath = $path;
                    break 2;
                }
            }
        }

        if (!$filePath) {
            if ($externalUrlFallback && preg_match('#^https?://#i', $externalUrlFallback)) {
                return redirect()->away($externalUrlFallback);
            }
            abort(404, 'File VBG tidak ditemukan.');
        }

        // Security check: prevent directory traversal
        $realPath = realpath($filePath);
        $storagePath = realpath(storage_path('app/public'));
        $uploadsPath = realpath(public_path('uploads'));
        $eventDocsPublicPath = realpath(public_path('events/docs'));

        if (!$realPath) {
            abort(403, 'Akses file VBG ditolak.');
        }

        // Check if file is in allowed directories
        $isInStorage = $storagePath && str_starts_with($realPath, $storagePath);
        $isInUploads = $uploadsPath && str_starts_with($realPath, $uploadsPath);
        $isInPublicEventDocs = $eventDocsPublicPath && str_starts_with($realPath, $eventDocsPublicPath);

        if (!($isInStorage || $isInUploads || $isInPublicEventDocs)) {
            abort(403, 'Akses file VBG ditolak.');
        }

        // Get MIME type
        $mimeType = mime_content_type($filePath);
        if (!$mimeType) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
            ];
            $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
        }

        // Generate filename
        $fileName = 'VBG_' . $event->id . '_' . \Illuminate\Support\Str::slug(substr($event->title, 0, 20)) . '.' . pathinfo($filePath, PATHINFO_EXTENSION);

        return response()->download($filePath, $fileName, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
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

        // Safety net: ensure unit slots from template exist before opening studio.
        $this->ensureTemplateStructureExists($course);
        $this->ensureQuizSlotPerUnit($course);

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

        $uploadedMaterials = $allModules
            ->filter(function ($module) {
                return in_array($module->type, ['pdf', 'video']) && !empty($module->content_url);
            })
            ->map(function ($module) use ($course) {
                $unitNo = (int) floor((((int) $module->order_no) - 1) / 3) + 1;

                return [
                    'module_id' => (int) $module->id,
                    'order_no' => (int) $module->order_no,
                    'unit_no' => $unitNo,
                    'type' => (string) $module->type,
                    'title' => (string) ($module->title ?? ''),
                    'file_name' => (string) ($module->file_name ?: basename((string) $module->content_url)),
                    'view_url' => route('trainer.courses.studio.material.view', [$course->id, $module->id]),
                    'updated_at' => optional($module->updated_at)->toDateTimeString(),
                ];
            })
            ->values();

        if ($activeUnitModules->isEmpty()) {
            return redirect()->route('trainer.courses')->with('error', 'Silabus untuk bab ini belum tersedia.');
        }

        $unitTitle = "Modul " . ($unitIndex + 1);

        return view('trainer.content-studio', compact('course', 'activeUnitModules', 'unitTitle', 'unitIndex', 'uploadedMaterials'));
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

        $completedEventsCount = $trainer->eventsAsTrainer()
            ->whereDate('event_date', '<', now()->toDateString())
            ->count();

        $completedCoursesCount = $trainer->coursesAsTrainer()
            ->where('status', 'approved')
            ->whereNotNull('approved_at')
            ->where('approved_at', '<', now())
            ->count();

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

        $trainerCertificates = TrainerCertificate::query()
            ->with('certifiable')
            ->where('trainer_id', $trainer->id)
            ->where('status', 'sent')
            ->latest('issued_at')
            ->latest('created_at')
            ->take(3)
            ->get();

        $totalCertificates = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->where('status', 'sent')
            ->count();

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
            'trainerCertificates',
            'expertiseTags',
            'totalCourses',
            'totalEvents',
            'completedEventsCount',
            'completedCoursesCount',
            'totalCertificates',
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
            'academic_title' => 'nullable|string|max:120',
            'profession' => 'nullable|string|max:100',
            'institution' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'bank_name' => 'nullable|string|max:120',
            'bank_account_number' => 'nullable|string|max:60',
            'bank_account_holder' => 'nullable|string|max:150',
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
        $autoReplacedCount = 0;
        $adaptedSlotCount = 0;
        $rejectedFiles = [];
        $updatedModules = [];

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
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);

                $replaceModule->refresh();
                $updatedModules[] = $this->mapStudioMaterialModule((int) $id, $replaceModule);

                $course->update(['status' => 'pending_review']);

                return response()->json([
                    'success' => true,
                    'message' => 'File berhasil diganti.',
                    'updated_modules' => $updatedModules,
                    'rejected_files' => [],
                ]);
            }

            $modulesByType = \App\Models\CourseModule::where('course_id', $id)
                ->whereIn('id', $targetIds)
                ->get()
                ->groupBy('type')
                ->map(fn($group) => $group->values());

            // Track available types untuk pesan error yang lebih informatif
            $availableTypes = [];
            foreach ($modulesByType as $slotType => $slotModules) {
                if (in_array($slotType, ['pdf', 'video'])) {
                    $availableTypes[$slotType] = [
                        'count' => $slotModules->count(),
                        'filled' => $slotModules->filter(fn($m) => !empty($m->content_url))->count(),
                    ];
                }
            }

            $slotStateByType = [];
            foreach ($modulesByType as $slotType => $slotModules) {
                $emptyQueue = [];
                foreach ($slotModules as $idx => $slotModule) {
                    if (empty($slotModule->content_url)) {
                        $emptyQueue[] = $idx;
                    }
                }

                $slotStateByType[$slotType] = [
                    'modules' => $slotModules,
                    'empty_queue' => $emptyQueue,
                    'replace_cursor' => 0,
                ];
            }

            foreach ($request->file('files') as $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                $type = in_array($ext, ['mp4']) ? 'video' : 'pdf';
                $isAutoReplace = false;
                $usedCompatibleSlot = false;
                $stateKey = $type;

                // Flow mirip event: isi slot kosong dulu, jika penuh auto-overwrite slot tipe yang sama.
                $state = $slotStateByType[$stateKey] ?? null;

                // Backward compatibility for legacy courses:
                // if exact slot type does not exist in this unit, reuse other material slot (video/pdf).
                if ((!$state || $state['modules']->isEmpty()) && in_array($type, ['pdf', 'video'], true)) {
                    $fallbackStateKey = $type === 'pdf' ? 'video' : 'pdf';
                    $fallbackState = $slotStateByType[$fallbackStateKey] ?? null;
                    if ($fallbackState && !$fallbackState['modules']->isEmpty()) {
                        $state = $fallbackState;
                        $stateKey = $fallbackStateKey;
                        $usedCompatibleSlot = true;
                    }
                }

                if (!$state || $state['modules']->isEmpty()) {
                    $module = null;
                } elseif (!empty($state['empty_queue'])) {
                    $slotIdx = array_shift($state['empty_queue']);
                    $module = $state['modules']->get($slotIdx);
                    $slotStateByType[$stateKey] = $state;
                } else {
                    $slotCount = $state['modules']->count();
                    $slotIdx = $state['replace_cursor'] % $slotCount;
                    $module = $state['modules']->get($slotIdx);
                    $state['replace_cursor'] = $state['replace_cursor'] + 1;
                    $slotStateByType[$stateKey] = $state;
                    $isAutoReplace = true;
                }

                if ($module) {
                    $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());

                    if ($module->content_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($module->content_url)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($module->content_url);
                    }

                    $filepath = $file->storeAs('courses/' . $id . '/materials', $filename, 'public');

                    $module->update([
                        'type' => $type,
                        'content_url' => $filepath,
                        'file_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                    $module->refresh();
                    $updatedModules[] = $this->mapStudioMaterialModule((int) $id, $module);
                    $uploadedCount++;
                    if ($isAutoReplace) {
                        $autoReplacedCount++;
                    }
                    if ($usedCompatibleSlot) {
                        $adaptedSlotCount++;
                    }
                } else {
                    $suffix = ' (tipe ' . strtoupper($type) . ' tidak punya slot di bab ini)';
                    $rejectedFiles[] = $file->getClientOriginalName() . $suffix;
                }
            }

            if ($uploadedCount > 0) {
                $course->update(['status' => 'pending_review']);
            }

            // Build helpful error message
            $msg = "$uploadedCount file berhasil diunggah.";
            if ($autoReplacedCount > 0) {
                $msg .= " {$autoReplacedCount} file otomatis mengganti file lama pada slot yang sama.";
            }
            if ($adaptedSlotCount > 0) {
                $msg .= " {$adaptedSlotCount} file ditempatkan ke slot materi kompatibel untuk menyesuaikan struktur course lama.";
            }
            if (count($rejectedFiles) > 0) {
                $msg .= " File (" . implode(", ", $rejectedFiles) . ") ditolak.";

                // Add info about available types
                if (!empty($availableTypes)) {
                    $typeInfo = [];
                    foreach ($availableTypes as $type => $info) {
                        $typeInfo[] = strtoupper($type) . " ({$info['filled']}/{$info['count']} terisi)";
                    }
                    $msg .= " Slot tersedia di bab ini: " . implode(", ", $typeInfo) . ".";
                } else {
                    $msg .= " Tidak ada slot File/Video di bab ini - periksa modul yang tersedia.";
                }
            }

            if ($uploadedCount === 0) {
                return response()->json([
                    'success' => false,
                    'error' => $msg,
                    'updated_modules' => [],
                    'available_types' => $availableTypes,
                    'rejected_files' => $rejectedFiles,
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => $msg,
                'updated_modules' => $updatedModules,
                'rejected_files' => $rejectedFiles,
                'available_types' => $availableTypes,
            ]);
        }
        return response()->json(['success' => false, 'error' => 'Tidak ada file.']);
    }

    public function uploadEventMaterials(Request $request, $id)
    {
        $request->validate([
            'files' => 'required|array|min:1|max:1',
            'files.*' => 'required|file|mimes:pdf,mp4,pptx,ppt,docx,doc|max:512000'
        ]);

        $event = \App\Models\Event::where('id', $id)
            ->where('trainer_id', Auth::id())
            ->firstOrFail();

        if (!empty($event->material_deadline) && now()->gt($event->material_deadline)) {
            return response()->json([
                'success' => false,
                'error' => 'Batas pengumpulan materi sudah lewat. Silakan hubungi admin trainer.',
            ], 422);
        }

        if (!$request->hasFile('files')) {
            return response()->json(['success' => false, 'error' => 'Tidak ada file.']);
        }

        $storedFiles = [];
        $primaryMaterialPath = null;
        $latestImagePath = null;
        $latestModuleDocPath = null;

        foreach ($request->file('files') as $file) {
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $filepath = $file->storeAs('events/' . $event->id . '/materials', $filename, 'public');

            $storedFiles[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $filepath,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];

            if ($primaryMaterialPath === null) {
                $primaryMaterialPath = $filepath;
            }
        }

        if (!empty($primaryMaterialPath)) {
            $event->update([
                'module_path' => $primaryMaterialPath,
                'material_status' => 'pending_review',
            ]);
            if (str_starts_with((string) $file->getMimeType(), 'image/')) {
                $latestImagePath = $filepath;
            } else {
                // Treat non-image docs as a module submission (pending admin verification).
                $ext = strtolower((string) $file->getClientOriginalExtension());
                if (in_array($ext, ['pdf', 'ppt', 'pptx', 'doc', 'docx'], true)) {
                    $latestModuleDocPath = $filepath;
                }
            }
        }

        $updates = [];
        if ($latestImagePath) {
            $updates['vbg_path'] = $latestImagePath;
        }
        if ($latestModuleDocPath) {
            $updates['module_path'] = $latestModuleDocPath;
        }
        if (!empty($updates)) {
            $event->update($updates);
        }

        return response()->json([
            'success' => true,
            'message' => 'Materi event berhasil diunggah.',
            'files' => $storedFiles,
            'module_path' => $primaryMaterialPath,
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
        $savedQuestions = [];
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

            $savedQuestions[] = [
                'text' => (string) $quizQuestion->question,
                'weight' => (int) $quizQuestion->points,
                'options' => collect($questionData['options'] ?? [])->map(fn($opt) => (string) $opt)->values()->all(),
                'correctAnswer' => (int) ($questionData['correctAnswer'] ?? 0),
            ];
        }

        $course->update(['status' => 'pending_review']);

        return response()->json([
            'success' => true,
            'message' => 'Kuis Bab berhasil disimpan!',
            'quiz_module' => [
                'id' => (int) $quizModule->id,
                'title' => (string) ($quizModule->title ?: ('Quiz Unit')),
                'order_no' => (int) $quizModule->order_no,
                'questions_count' => count($savedQuestions),
                'updated_at' => optional($quizModule->fresh()->updated_at)->toDateTimeString(),
                'questions' => $savedQuestions,
            ],
        ]);
    }

    /**
     * Accept event invitation from trainer
     */
    public function acceptEventInvitation(Request $request, $id)
    {
        $trainerId = Auth::id();
        $event = \App\Models\Event::where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        // Update trainer notification status
        $notification = \App\Models\TrainerNotification::where('trainer_id', $trainerId)
            ->where('type', 'event_invitation')
            ->where(function ($q) use ($id) {
                $q->whereJsonContains('data->entity_id', $id)
                    ->orWhereJsonContains('data->entity_id', (string) $id);
            })
            ->first();

        if ($notification) {
            $notification->update([
                'invitation_status' => 'accepted',
                'responded_at' => now(),
            ]);
        }

        return back()->with('success', 'Undangan event berhasil diterima. Silakan upload materi untuk event ini.');
    }

    /**
     * Reject event invitation from trainer
     */
    public function rejectEventInvitation(Request $request, $id)
    {
        $trainerId = Auth::id();
        $event = \App\Models\Event::where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        // Update trainer notification status
        $notification = \App\Models\TrainerNotification::where('trainer_id', $trainerId)
            ->where('type', 'event_invitation')
            ->where(function ($q) use ($id) {
                $q->whereJsonContains('data->entity_id', $id)
                    ->orWhereJsonContains('data->entity_id', (string) $id);
            })
            ->first();

        if ($notification) {
            $notification->update([
                'invitation_status' => 'rejected',
                'responded_at' => now(),
            ]);
        }

        return back()->with('success', 'Undangan event berhasil ditolak.');
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
        $this->ensureTrainerCertificatesSynced($trainer);

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

        $historyItems = $historyItems->sortByDesc(fn($item) => $item['date'] ?? now());

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
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
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
