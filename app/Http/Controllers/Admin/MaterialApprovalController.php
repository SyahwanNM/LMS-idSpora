<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Event;
use App\Models\TrainerNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialApprovalController extends Controller
{
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
            if ($content === '' || $content === 'quiz_submitted') {
                $missingItems[] = $slotLabel . ' (file belum diupload)';
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
                $entityType = method_exists($notification, 'effectiveEntityType')
                    ? $notification->effectiveEntityType()
                    : (string) data_get($notification->data, 'entity_type');
                $entityId = method_exists($notification, 'effectiveEntityId')
                    ? $notification->effectiveEntityId()
                    : (int) data_get($notification->data, 'entity_id');

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
                    ->orWhereIn('material_status', ['pending', 'pending_review']);
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

        $pendingEventModules = $pendingEventModulesQuery
            ->orderByDesc('module_submitted_at')
            ->orderByDesc('created_at')
            ->orderByDesc('event_date')
            ->get([
                'id',
                'trainer_id',
                'title',
                'jenis',
                'event_date',
                'module_path',
                'module_submitted_at',
                'created_at',
                'updated_at',
            ]);

        // Backward-compat display: older rows may not have module_submitted_at filled.
        // For pending items, updated_at usually reflects the module upload time.
        $pendingEventModules->transform(function (Event $event) {
            if (empty($event->module_submitted_at) && !empty($event->module_path)) {
                $event->setAttribute('module_submitted_at', $event->updated_at);
            }
            return $event;
        });

        // Statistics
        $totalPending = Course::where('status', 'pending_review')->count()
            + Event::query()
                ->whereNotNull('module_path')
                ->where(function ($q) {
                    $q->whereNull('material_status')
                        ->orWhereIn('material_status', ['pending', 'pending_review']);
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
        $material->load([
            'trainer',
            'category',
            'modules.quizQuestions',
            'modules.quizQuestions.answers',
            'reviews'
        ]);

        $structureCompleteness = $this->assessStructureCompleteness($material);

        $uploadedModules = $material->modules;
        $uploadedModulesCount = $uploadedModules->count();

        return view('admin.material.show', compact('material', 'structureCompleteness', 'uploadedModules', 'uploadedModulesCount'));
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

        $contentPath = trim((string) ($module->content_url ?? ''));
        if ($contentPath === '' || $contentPath === 'quiz_submitted') {
            abort(404, 'File modul tidak tersedia.');
        }

        if (str_starts_with($contentPath, 'storage/')) {
            $contentPath = ltrim(substr($contentPath, 8), '/');
        }

        $contentPath = ltrim($contentPath, '/');

        if (!Storage::disk('public')->exists($contentPath)) {
            abort(404, 'File modul tidak ditemukan di storage.');
        }

        $absolutePath = Storage::disk('public')->path($contentPath);
        $downloadName = $module->file_name ?: basename($contentPath);
        $detectedMime = @mime_content_type($absolutePath);
        $mime = (string) ($module->mime_type ?: $detectedMime ?: 'application/octet-stream');

        if ($request->boolean('download')) {
            return response()->download($absolutePath, $downloadName, [
                'Content-Type' => $mime,
            ]);
        }

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    /**
     * Approve individual module
     */
    public function approveModule(Course $material, CourseModule $module)
    {
        if ((int) $module->course_id !== (int) $material->id) {
            abort(404, 'Modul tidak ditemukan pada materi ini.');
        }

        $module->update([
            'review_status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'review_rejection_reason' => null
        ]);

        return back()->with('success', "Modul {$module->title} berhasil disetujui.");
    }

    /**
     * Reject individual module
     */
    public function rejectModule(Request $request, Course $material, CourseModule $module)
    {
        if ((int) $module->course_id !== (int) $material->id) {
            abort(404, 'Modul tidak ditemukan pada materi ini.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

        $module->update([
            'review_status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'review_rejection_reason' => $request->rejection_reason
        ]);

        return back()->with('success', "Modul {$module->title} berhasil ditolak.");
    }

    /**
     * Approve material
     */
    public function approve(Course $material)
    {
        $material->loadMissing(['trainer', 'modules']);

        if ($material->modules->isEmpty()) {
            return redirect()
                ->route('admin.material.show', $material)
                ->with('error', 'Belum ada materi yang diupload trainer. Approval hanya bisa dilakukan untuk materi yang sudah ada.');
        }

        $material->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'rejection_reason' => null,
            'rejected_at' => null,
        ]);

        if (!empty($material->trainer_id)) {
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

        $material->update([
            'status' => 'rejected',
            'rejection_reason' => $rejectionReason,
            'rejected_at' => now(),
            'approved_by' => Auth::id(), // Track who rejected it
        ]);

        if (!empty($material->trainer_id)) {
            TrainerNotification::create([
                'trainer_id' => (int) $material->trainer_id,
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

    /**
     * Show all approved materials
     */
    public function approved(Request $request)
    {
        $query = Course::with(['trainer', 'category'])
            ->where('status', 'approved')
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

        $approvedMaterials = $query->orderBy('approved_at', 'desc')->paginate(15);

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

