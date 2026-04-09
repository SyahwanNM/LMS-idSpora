<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Event;
use App\Models\TrainerNotification;
use App\Models\User;
use App\Services\TrainerActivityService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialApprovalController extends Controller
{
    private function resolveCourseNotificationRecipientIds(Course $course): array
    {
        $ids = [];

        if (!empty($course->trainer_id)) {
            $ids[] = (int) $course->trainer_id;
        }

        $invitedTrainerIds = TrainerNotification::query()
            ->where('type', 'course_invitation')
            ->where('data->entity_type', 'course')
            ->where('data->entity_id', (int) $course->id)
            ->pluck('trainer_id')
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->values()
            ->all();

        $ids = array_merge($ids, $invitedTrainerIds);

        return collect($ids)
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeModuleReviewStatus(?string $status): string
    {
        $status = strtolower(trim((string) $status));
        return in_array($status, ['approved', 'rejected', 'pending_review'], true)
            ? $status
            : 'pending_review';
    }

    private function moduleReviewSortRank(?string $status): int
    {
        return match ($this->normalizeModuleReviewStatus($status)) {
            'pending_review' => 0,
            'rejected' => 1,
            'approved' => 2,
            default => 3,
        };
    }

    private function refreshCourseReviewStatus(Course $course): void
    {
        $course->loadMissing(['modules.quizQuestions']);

        $uploadedModules = $course->modules
            ->filter(fn(CourseModule $module) => $this->isUploadedModule($module))
            ->values();

        if ($uploadedModules->isEmpty()) {
            $course->update([
                'status' => 'pending_review',
                'approved_at' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);
            return;
        }

        $hasRejected = false;
        $allApproved = true;
        $reasons = [];

        foreach ($uploadedModules as $module) {
            $moduleStatus = $this->normalizeModuleReviewStatus($module->review_status);
            if ($moduleStatus === 'rejected') {
                $hasRejected = true;
                if (!empty($module->review_rejection_reason)) {
                    $reasons[] = $module->title . ': ' . $module->review_rejection_reason;
                }
            }

            if ($moduleStatus !== 'approved') {
                $allApproved = false;
            }
        }

        if ($hasRejected) {
            $course->update([
                'status' => 'rejected',
                'approved_at' => null,
                'rejected_at' => now(),
                'approved_by' => Auth::id(),
                'rejection_reason' => !empty($reasons) ? implode("\n", $reasons) : 'Beberapa modul masih perlu revisi.',
            ]);
            return;
        }

        if ($allApproved) {
            $course->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);
            return;
        }

        $course->update([
            'status' => 'pending_review',
            'approved_at' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);
    }

    private function isUploadedModule(CourseModule $module): bool
    {
        if ($module->isQuiz()) {
            return (int) ($module->quizQuestions->count() ?? 0) > 0;
        }

        $content = trim((string) ($module->content_url ?? ''));
        if ($content !== '' && $content !== 'quiz_submitted') {
            return true;
        }

        // Text-first module authoring stores content in description HTML.
        if ($module->isPdf()) {
            $description = trim((string) ($module->description ?? ''));
            return $description !== '';
        }

        return false;
    }

    private function normalizePublicPath(?string $path): string
    {
        $normalized = ltrim((string) $path, '/');

        if ($normalized === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $normalized)) {
            $urlPath = parse_url($normalized, PHP_URL_PATH);
            $normalized = is_string($urlPath) ? ltrim($urlPath, '/') : '';
        }

        $normalized = str_replace('\\', '/', $normalized);
        $normalized = preg_replace('#^\./#', '', $normalized) ?? $normalized;
        $normalized = ltrim($normalized, '/');

        if (str_starts_with($normalized, 'public/')) {
            $normalized = ltrim(substr($normalized, 7), '/');
        }

        if (str_starts_with($normalized, 'storage/app/public/')) {
            $normalized = ltrim(substr($normalized, 19), '/');
        }

        if (str_starts_with($normalized, 'storage/')) {
            $normalized = ltrim(substr($normalized, 8), '/');
        }

        if (str_starts_with($normalized, 'uploads/')) {
            $normalized = ltrim(substr($normalized, 8), '/');
        }

        return $normalized;
    }

    private function resolveModuleFilePath(CourseModule $module): ?string
    {
        $sourcePath = trim((string) ($module->content_url ?? ''));
        if ($sourcePath === '' || $sourcePath === 'quiz_submitted') {
            return null;
        }

        $normalized = $this->normalizePublicPath($sourcePath);
        if ($normalized === '') {
            return null;
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
                return $path;
            }
        }

        return null;
    }

    private function guessMimeType(CourseModule $module): string
    {
        $mime = (string) ($module->mime_type ?? '');
        if ($mime !== '') {
            return $mime;
        }

        try {
            $absolutePath = $this->resolveModuleFilePath($module);
            if ($absolutePath && file_exists($absolutePath)) {
                $detected = @mime_content_type($absolutePath);
                if (is_string($detected) && $detected !== '') {
                    return $detected;
                }
            }
        } catch (\Throwable $e) {
            // ignore detection failures and fall back to extension-based guessing
        }

        $ext = strtolower(pathinfo((string) $module->content_url, PATHINFO_EXTENSION));

        return match ($ext) {
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg', 'ogv' => 'video/ogg',
            'pdf' => 'application/pdf',
            default => ($module->isVideo() ? 'video/mp4' : 'application/octet-stream'),
        };
    }

    private function assessStructureCompleteness(Course $course): array
    {
        $modules = $course->modules()->withCount('quizQuestions')->orderBy('order_no')->get();

        if ($modules->isEmpty()) {
            return [
                'is_complete' => false,
                'missing_items' => ['Struktur modul belum tersedia untuk course ini.'],
                'missing_count' => 1,
                'total_modules' => 0,
            ];
        }

        $missingItems = [];
        foreach ($modules as $module) {
            $title = trim((string) ($module->title ?? 'Untitled'));
            $slotLabel = '#' . (int) ($module->order_no ?? 0) . ' - ' . $title;

            if ($module->isQuiz()) {
                if ((int) ($module->quiz_questions_count ?? 0) <= 0) {
                    $missingItems[] = $slotLabel . ' (quiz belum diisi)';
                }
                continue;
            }

            $content = trim((string) ($module->content_url ?? ''));
            $description = trim((string) ($module->description ?? ''));
            $hasTextContent = $module->isPdf() && $description !== '';
            if (($content === '' || $content === 'quiz_submitted') && !$hasTextContent) {
                $missingItems[] = $slotLabel . ' (file atau konten teks belum diupload)';
            }
        }

        return [
            'is_complete' => count($missingItems) === 0,
            'missing_items' => $missingItems,
            'missing_count' => count($missingItems),
            'total_modules' => (int) $modules->count(),
        ];
    }

    private function buildDeadlineMonitoring(Collection $materials): array
    {
        if ($materials->isEmpty()) {
            return [];
        }

        $trainerIds = $materials->pluck('trainer_id')->filter()->unique()->values();
        if ($trainerIds->isEmpty()) {
            return [];
        }

        $courseIds = $materials->pluck('id')->map(fn($id) => (int) $id)->values()->all();

        $notifications = TrainerNotification::query()
            ->where('type', 'course_invitation')
            ->whereIn('trainer_id', $trainerIds)
            ->orderByDesc('created_at')
            ->get();

        $map = [];
        foreach ($materials as $material) {
            $map[$material->id] = [
                'has_deadline' => false,
                'deadline_text' => 'Belum ditentukan',
                'status' => 'neutral',
                'status_text' => 'Tanpa deadline',
            ];

            if (empty($material->trainer_id)) {
                continue;
            }

            $invite = $notifications->first(function (TrainerNotification $notification) use ($material, $courseIds) {
                if ((int) $notification->trainer_id !== (int) $material->trainer_id) {
                    return false;
                }
                $entityType = (string) data_get($notification->data, 'entity_type');
                $entityId = (int) data_get($notification->data, 'entity_id');

                return $entityType === 'course' && $entityId === (int) $material->id && in_array($entityId, $courseIds, true);
            });

            if (!$invite) {
                continue;
            }

            $dueAtRaw = data_get($invite->data, 'due_at');
            if (empty($dueAtRaw)) {
                continue;
            }

            try {
                $dueAt = Carbon::parse($dueAtRaw);
            } catch (\Throwable $e) {
                continue;
            }

            $submittedAt = $material->updated_at ? Carbon::parse($material->updated_at) : null;
            $isLate = $submittedAt ? $submittedAt->gt($dueAt) : Carbon::now()->gt($dueAt);

            $map[$material->id] = [
                'has_deadline' => true,
                'deadline_text' => $dueAt->format('d M Y H:i'),
                'status' => $isLate ? 'late' : 'on_time',
                'status_text' => $isLate ? 'Melewati deadline' : 'Tepat waktu',
            ];
        }

        return $map;
    }

    /**
     * Display queue of materials pending review
     */
    public function index(Request $request)
    {
        $query = Course::with(['trainer', 'category', 'modules'])
            ->where('status', 'pending_review')
            ->withCount('modules');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('trainer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sort functionality
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $deadlineFilter = (string) $request->get('deadline_filter', 'all');
        if (in_array($deadlineFilter, ['overdue', 'on_time', 'no_deadline'], true)) {
            $candidateMaterials = (clone $query)
                ->get(['id', 'trainer_id', 'updated_at']);

            $candidateMonitoring = $this->buildDeadlineMonitoring($candidateMaterials);

            $matchedIds = $candidateMaterials
                ->filter(function (Course $material) use ($candidateMonitoring, $deadlineFilter) {
                    $monitor = $candidateMonitoring[$material->id] ?? null;
                    $status = (string) ($monitor['status'] ?? 'neutral');

                    return match ($deadlineFilter) {
                        'overdue' => $status === 'late',
                        'on_time' => $status === 'on_time',
                        'no_deadline' => $status === 'neutral',
                        default => true,
                    };
                })
                ->pluck('id')
                ->values()
                ->all();

            if (empty($matchedIds)) {
                $query->whereRaw('1=0');
            } else {
                $query->whereIn('id', $matchedIds);
            }
        }

        $pendingMaterials = $query->paginate(15);

        $pendingEventModulesQuery = Event::query()
            ->with(['trainer:id,name,email,avatar'])
            ->whereNotNull('module_path')
            ->where(function ($q) {
                $q->whereNull('material_status')
                    ->orWhere('material_status', '')
                    ->orWhereNotIn('material_status', ['approved', 'rejected']);
            });

        if ($request->filled('search')) {
            $search = (string) $request->search;
            $pendingEventModulesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('trainer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (in_array($deadlineFilter, ['overdue', 'on_time', 'no_deadline'], true)) {
            $now = now();

            $pendingEventModulesQuery->where(function ($q) use ($deadlineFilter, $now) {
                if ($deadlineFilter === 'overdue') {
                    $q->whereNotNull('material_deadline')
                        ->where('material_deadline', '<', $now);
                    return;
                }

                if ($deadlineFilter === 'on_time') {
                    $q->whereNotNull('material_deadline')
                        ->where('material_deadline', '>=', $now);
                    return;
                }

                $q->whereNull('material_deadline');
            });
        }

        $pendingEventModules = $pendingEventModulesQuery
            ->orderByDesc('created_at')
            ->orderByDesc('event_date')
            ->get([
                'id',
                'trainer_id',
                'title',
                'jenis',
                'event_date',
                'material_deadline',
                'module_path',
                'created_at as module_submitted_at',
            ]);

        // Statistics
        $totalPending = Course::where('status', 'pending_review')->count()
            + Event::query()
                ->whereNotNull('module_path')
                ->where(function ($q) {
                    $q->whereNull('material_status')
                        ->orWhere('material_status', '')
                        ->orWhereNotIn('material_status', ['approved', 'rejected']);
                })
                ->count();
        $totalApproved = Course::where('status', 'approved')->count()
            + Event::query()
                ->whereNotNull('module_path')
                ->where('material_status', 'approved')
                ->count();
        $totalRejected = Course::where('status', 'rejected')->count()
            + Event::query()
                ->whereNotNull('module_path')
                ->where('material_status', 'rejected')
                ->count();

        $deadlineMonitoring = $this->buildDeadlineMonitoring($pendingMaterials->getCollection());

        return view('admin.material.approvals', compact(
            'pendingMaterials',
            'pendingEventModules',
            'totalPending',
            'totalApproved',
            'totalRejected',
            'deadlineMonitoring',
            'deadlineFilter'
        ));
    }

    /**
     * Display specific material for review with preview
     */
    public function show(Course $material)
    {
        // Load relationships
        $material->load([
            'trainer',
            'category',
            'modules.quizQuestions',
            'modules.quizQuestions.answers',
            'reviews'
        ]);

        $uploadedModules = $material->modules
            ->filter(fn(CourseModule $module) => $this->isUploadedModule($module))
            ->sortBy(function (CourseModule $module) {
                return [
                    $this->moduleReviewSortRank($module->review_status),
                    (int) ($module->order_no ?? 0),
                    (int) ($module->id ?? 0),
                ];
            })
            ->values();

        $uploadedModulesCount = $uploadedModules->count();

        $moduleReviewStats = [
            'pending' => $uploadedModules->filter(fn(CourseModule $module) => $this->normalizeModuleReviewStatus($module->review_status) === 'pending_review')->count(),
            'rejected' => $uploadedModules->filter(fn(CourseModule $module) => $this->normalizeModuleReviewStatus($module->review_status) === 'rejected')->count(),
            'approved' => $uploadedModules->filter(fn(CourseModule $module) => $this->normalizeModuleReviewStatus($module->review_status) === 'approved')->count(),
        ];

        $structureCompleteness = $this->assessStructureCompleteness($material);

        return view('admin.material.show', compact(
            'material',
            'structureCompleteness',
            'uploadedModules',
            'uploadedModulesCount',
            'moduleReviewStats'
        ));
    }

    /**
     * Stream module file for admin review (inline) or download.
     */
    public function streamModule(Request $request, Course $material, CourseModule $module)
    {
        if ((int) $module->course_id !== (int) $material->id) {
            abort(404, 'Modul tidak ditemukan pada materi ini.');
        }

        if ($module->isQuiz()) {
            abort(404, 'Modul kuis tidak memiliki file untuk dipreview.');
        }

        $absolutePath = $this->resolveModuleFilePath($module);
        if (!$absolutePath) {
            $textHtml = trim((string) ($module->description ?? ''));
            if ($module->isPdf() && $textHtml !== '') {
                $sanitizedHtml = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $textHtml) ?? $textHtml;

                $document = '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
                    . '<title>Preview Materi</title>'
                    . '<style>body{font-family:Arial,sans-serif;line-height:1.65;color:#1e293b;padding:24px;max-width:900px;margin:0 auto}img{max-width:100%;height:auto;border-radius:10px}pre{background:#0f172a;color:#e2e8f0;padding:12px;border-radius:8px;overflow:auto}code{font-family:Consolas,Monaco,monospace}</style>'
                    . '</head><body>' . $sanitizedHtml . '</body></html>';

                return response($document, 200, [
                    'Content-Type' => 'text/html; charset=UTF-8',
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                    'X-Frame-Options' => 'SAMEORIGIN',
                ]);
            }

            abort(404, 'File modul tidak tersedia.');
        }

        $downloadName = $module->file_name ?: basename($absolutePath);
        $mime = $this->guessMimeType($module);
        $range = $request->header('Range');
        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $downloadName . '"',
            'Accept-Ranges' => 'bytes',
        ];

        if ($request->boolean('download')) {
            return response()->download($absolutePath, $downloadName, $headers);
        }

        if (is_string($range) && preg_match('/bytes=(\d*)-(\d*)/i', $range, $matches)) {
            $fileSize = filesize($absolutePath);
            $start = ($matches[1] !== '') ? (int) $matches[1] : 0;
            $end = ($matches[2] !== '') ? (int) $matches[2] : ($fileSize - 1);
            $end = min($end, $fileSize - 1);
            if ($start > $end) {
                $start = 0;
            }

            $length = $end - $start + 1;
            $headers['Content-Length'] = $length;
            $headers['Content-Range'] = 'bytes ' . $start . '-' . $end . '/' . $fileSize;

            return response()->stream(function () use ($absolutePath, $start, $end) {
                $chunkSize = 8192;
                $handle = fopen($absolutePath, 'rb');
                if ($handle === false) {
                    return;
                }

                fseek($handle, $start);
                $remaining = $end - $start + 1;

                while ($remaining > 0 && !feof($handle)) {
                    $read = ($remaining > $chunkSize) ? $chunkSize : $remaining;
                    $buffer = fread($handle, $read);
                    if ($buffer === '' || $buffer === false) {
                        break;
                    }
                    echo $buffer;
                    flush();
                    $remaining -= strlen($buffer);
                }

                fclose($handle);
            }, 206, $headers);
        }

        $headers['Cache-Control'] = 'no-store, no-cache, must-revalidate, max-age=0';
        $headers['Content-Length'] = filesize($absolutePath);

        return response()->file($absolutePath, $headers);
    }

    /**
     * Approve material
     */
    public function approve(Course $material)
    {
        $material->loadMissing(['trainer', 'modules.quizQuestions']);

        $uploadedModules = $material->modules
            ->filter(fn(CourseModule $module) => $this->isUploadedModule($module));

        if ($uploadedModules->isEmpty()) {
            return redirect()
                ->route('admin.material.show', $material)
                ->with('error', 'Belum ada materi yang diupload trainer. Approval hanya bisa dilakukan untuk materi yang sudah diupload.');
        }

        foreach ($uploadedModules as $module) {
            $module->update([
                'review_status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'review_rejection_reason' => null,
            ]);
        }

        $this->refreshCourseReviewStatus($material);

        if (!empty($material->trainer_id)) {
            $trainer = User::query()->find((int) $material->trainer_id);
            if ($trainer) {
                app(TrainerActivityService::class)->refresh($trainer);
            }

            TrainerNotification::create([
                'trainer_id' => (int) $material->trainer_id,
                'type' => 'course_material_approved',
                'title' => 'Materi Course Diterima',
                'message' => 'Materi course "' . $material->name . '" telah disetujui oleh admin.',
                'data' => [
                    'entity_type' => 'course',
                    'entity_id' => (int) $material->id,
                    'url' => route('trainer.detail-course', $material->id),
                ],
                'expires_at' => now()->addDays(30),
            ]);
        }

        return redirect()
            ->route('admin.material.approvals')
            ->with('success', "Materi yang sudah diupload pada \"{$material->name}\" berhasil disetujui!");
    }

    /**
     * Reject material with reason
     */
    public function reject(Request $request, Course $material)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        $material->loadMissing(['trainer']);
        $rejectionReason = (string) $request->rejection_reason;

        $material->loadMissing(['modules.quizQuestions']);

        $uploadedModules = $material->modules
            ->filter(fn(CourseModule $module) => $this->isUploadedModule($module));

        foreach ($uploadedModules as $module) {
            $module->update([
                'review_status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'review_rejection_reason' => $rejectionReason,
            ]);
        }

        $this->refreshCourseReviewStatus($material);

        $recipientIds = $this->resolveCourseNotificationRecipientIds($material);
        foreach ($recipientIds as $trainerId) {
            TrainerNotification::create([
                'trainer_id' => (int) $trainerId,
                'type' => 'course_material_rejected',
                'title' => 'Materi Course Perlu Revisi',
                'message' => 'Materi course "' . $material->name . '" perlu revisi. Catatan admin: ' . $rejectionReason,
                'data' => [
                    'entity_type' => 'course',
                    'entity_id' => (int) $material->id,
                    'rejection_reason' => $rejectionReason,
                    'url' => route('trainer.courses.studio', $material->id),
                ],
                'expires_at' => now()->addDays(30),
            ]);
        }

        return redirect()
            ->route('admin.material.approvals')
            ->with('success', "Materi \"{$material->name}\" ditolak dan catatan revisi telah dikirim ke trainer.");
    }

    public function approveModule(Course $material, CourseModule $module)
    {
        if ((int) $module->course_id !== (int) $material->id) {
            return redirect()->route('admin.material.show', $material)->with('error', 'Modul tidak valid untuk course ini.');
        }

        if (!$this->isUploadedModule($module)) {
            return redirect()->route('admin.material.show', $material)->with('error', 'Modul belum memiliki materi untuk direview.');
        }

        $module->update([
            'review_status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'review_rejection_reason' => null,
        ]);

        $this->refreshCourseReviewStatus($material);

        return redirect()->route('admin.material.show', $material)->with('success', 'Modul berhasil disetujui.');
    }

    public function rejectModule(Request $request, Course $material, CourseModule $module)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ]);

        if ((int) $module->course_id !== (int) $material->id) {
            return redirect()->route('admin.material.show', $material)->with('error', 'Modul tidak valid untuk course ini.');
        }

        if (!$this->isUploadedModule($module)) {
            return redirect()->route('admin.material.show', $material)->with('error', 'Modul belum memiliki materi untuk direview.');
        }

        $module->update([
            'review_status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'review_rejection_reason' => (string) $request->rejection_reason,
        ]);

        $recipientIds = $this->resolveCourseNotificationRecipientIds($material);
        foreach ($recipientIds as $trainerId) {
            TrainerNotification::create([
                'trainer_id' => (int) $trainerId,
                'type' => 'course_material_rejected',
                'title' => 'Modul Course Perlu Revisi',
                'message' => 'Modul "' . $module->title . '" pada course "' . $material->name . '" ditolak. Catatan admin: ' . (string) $request->rejection_reason,
                'data' => [
                    'entity_type' => 'course',
                    'entity_id' => (int) $material->id,
                    'module_id' => (int) $module->id,
                    'rejection_reason' => (string) $request->rejection_reason,
                    'url' => route('trainer.courses.studio', $material->id),
                ],
                'expires_at' => now()->addDays(30),
            ]);
        }

        $this->refreshCourseReviewStatus($material);

        return redirect()->route('admin.material.show', $material)->with('success', 'Modul ditolak. Catatan revisi tersimpan.');
    }

    /**
     * Show all approved materials
     */
    public function approved(Request $request)
    {
        $query = Course::with(['trainer', 'category'])
            ->where(function ($q) {
                $q->where('status', 'approved')
                    ->orWhereHas('modules', function ($moduleQuery) {
                        $moduleQuery->where('review_status', 'approved')
                            ->where(function ($uploadedQuery) {
                                $uploadedQuery->where(function ($contentQuery) {
                                    $contentQuery->whereNotNull('content_url')
                                        ->where('content_url', '!=', '')
                                        ->where('content_url', '!=', 'quiz_submitted');
                                })->orWhere(function ($textQuery) {
                                    $textQuery->where('type', 'pdf')
                                        ->whereNotNull('description')
                                        ->where('description', '!=', '');
                                })->orWhere(function ($quizQuery) {
                                    $quizQuery->where('type', 'quiz')
                                        ->whereHas('quizQuestions');
                                });
                            });
                    });
            })
            ->withCount('modules');

        $approvedEventModulesQuery = Event::query()
            ->with(['trainer:id,name,email,avatar'])
            ->whereNotNull('module_path')
            ->where('material_status', 'approved');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('trainer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });

            $approvedEventModulesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('trainer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $deadlineFilter = (string) $request->get('deadline_filter', 'all');
        if (in_array($deadlineFilter, ['overdue', 'on_time', 'no_deadline'], true)) {
            $candidateMaterials = (clone $query)
                ->get(['id', 'trainer_id', 'updated_at']);

            $candidateMonitoring = $this->buildDeadlineMonitoring($candidateMaterials);

            $matchedIds = $candidateMaterials
                ->filter(function (Course $material) use ($candidateMonitoring, $deadlineFilter) {
                    $monitor = $candidateMonitoring[$material->id] ?? null;
                    $status = (string) ($monitor['status'] ?? 'neutral');

                    return match ($deadlineFilter) {
                        'overdue' => $status === 'late',
                        'on_time' => $status === 'on_time',
                        'no_deadline' => $status === 'neutral',
                        default => true,
                    };
                })
                ->pluck('id')
                ->values()
                ->all();

            if (empty($matchedIds)) {
                $query->whereRaw('1=0');
            } else {
                $query->whereIn('id', $matchedIds);
            }
        }

        $approvedMaterials = $query
            ->orderByRaw('COALESCE(approved_at, updated_at) DESC')
            ->paginate(15);

        $approvedEventModules = $approvedEventModulesQuery
            ->orderByDesc('material_approved_at')
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->get([
                'id',
                'trainer_id',
                'title',
                'jenis',
                'event_date',
                'module_path',
                'material_approved_at as module_verified_at',
            ]);

        $deadlineMonitoring = $this->buildDeadlineMonitoring($approvedMaterials->getCollection());

        return view('admin.material.approved', compact('approvedMaterials', 'approvedEventModules', 'deadlineMonitoring', 'deadlineFilter'));
    }

    /**
     * Show all rejected materials
     */
    public function rejected(Request $request)
    {
        $query = Course::with(['trainer', 'category'])
            ->where('status', 'rejected')
            ->withCount('modules');

        $rejectedEventModulesQuery = Event::query()
            ->with(['trainer:id,name,email,avatar'])
            ->whereNotNull('module_path')
            ->where('material_status', 'rejected');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('trainer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });

            $rejectedEventModulesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('trainer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $deadlineFilter = (string) $request->get('deadline_filter', 'all');
        if (in_array($deadlineFilter, ['overdue', 'on_time', 'no_deadline'], true)) {
            $candidateMaterials = (clone $query)
                ->get(['id', 'trainer_id', 'updated_at']);

            $candidateMonitoring = $this->buildDeadlineMonitoring($candidateMaterials);

            $matchedIds = $candidateMaterials
                ->filter(function (Course $material) use ($candidateMonitoring, $deadlineFilter) {
                    $monitor = $candidateMonitoring[$material->id] ?? null;
                    $status = (string) ($monitor['status'] ?? 'neutral');

                    return match ($deadlineFilter) {
                        'overdue' => $status === 'late',
                        'on_time' => $status === 'on_time',
                        'no_deadline' => $status === 'neutral',
                        default => true,
                    };
                })
                ->pluck('id')
                ->values()
                ->all();

            if (empty($matchedIds)) {
                $query->whereRaw('1=0');
            } else {
                $query->whereIn('id', $matchedIds);
            }
        }

        $rejectedMaterials = $query->orderBy('rejected_at', 'desc')->paginate(15);

        $rejectedEventModules = $rejectedEventModulesQuery
            ->orderByDesc('updated_at')
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->get([
                'id',
                'trainer_id',
                'title',
                'jenis',
                'event_date',
                'module_path',
                'updated_at as module_rejected_at',
                'material_rejection_reason as module_rejection_reason',
            ]);

        $deadlineMonitoring = $this->buildDeadlineMonitoring($rejectedMaterials->getCollection());

        return view('admin.material.rejected', compact('rejectedMaterials', 'rejectedEventModules', 'deadlineMonitoring', 'deadlineFilter'));
    }
}

