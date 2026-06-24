<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Event;
use App\Models\TrainerAssignment;
use App\Models\TrainerNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialApprovalController extends Controller
{
    private function syncLegacyEventMaterialsToAssignments(): void
    {
        $modules = \App\Models\EventTrainerModule::get();
        foreach ($modules as $module) {
            $assignment = \App\Models\TrainerAssignment::where('event_id', $module->event_id)
                ->where('trainer_id', $module->trainer_id)
                ->first();

            if (!$assignment) {
                $assignment = \App\Models\TrainerAssignment::create([
                    'trainer_id' => $module->trainer_id,
                    'event_id' => $module->event_id,
                    'status' => 'accepted',
                    'sla_upload_deadline' => now()->addDays(3),
                ]);
            }

            // Sync latest module path and status to assignment
            $trainerModules = \App\Models\EventTrainerModule::where('event_id', $module->event_id)
                ->where('trainer_id', $module->trainer_id)
                ->get();

            $latestModule = $trainerModules->sortByDesc('created_at')->first();
            if (!$latestModule) {
                continue;
            }

            $totalModules = $trainerModules->count();
            $approvedModules = $trainerModules->where('status', 'approved')->count();
            $rejectedModules = $trainerModules->where('status', 'rejected')->count();
            $pendingModules = $trainerModules->whereIn('status', ['pending_review', 'pending'])->count();

            $newStatus = 'pending_review';
            if ($totalModules === 0) {
                $newStatus = 'pending';
            } elseif ($pendingModules > 0) {
                $newStatus = 'pending_review';
            } elseif ($approvedModules === $totalModules) {
                $newStatus = 'approved';
            } elseif ($rejectedModules > 0) {
                $newStatus = 'rejected';
            }

            $assignment->update([
                'material_path' => $latestModule->path,
                'material_status' => $newStatus,
                'materials_uploaded_at' => $latestModule->created_at,
                'material_submitted_at' => $assignment->material_submitted_at ?: $latestModule->created_at,
            ]);
        }
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
        $this->syncLegacyEventMaterialsToAssignments();

        $query = Course::with(['trainer', 'category', 'modules'])
            ->where(function ($q) {
                $q->where('status', 'pending_review')
                    ->orWhereHas('modules', function ($mq) {
                        $mq->where('review_status', 'pending_review')
                            ->whereNotNull('content_url')
                            ->where('content_url', '!=', '');
                    });
            })
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

        $pendingEventsQuery = \App\Models\TrainerAssignment::query()
            ->whereHas('event')
            ->whereNotNull('material_path')
            ->where(function ($q) {
                $q->whereNull('material_status')
                    ->orWhereIn('material_status', ['pending', 'pending_review']);
            })
            ->with([
                'trainer:id,name,email,avatar',
                'event:id,title,event_date,event_time,location,material_deadline,jenis',
                'event.trainerModules' => function ($q) {
                    $q->where('status', 'pending_review');
                },
                'event.trainerModules.trainer:id,name,email,avatar'
            ]);

        if ($request->filled('search')) {
            $search = (string) $request->search;
            $pendingEventsQuery->where(function ($q) use ($search) {
                $q->whereHas('event', function ($eq) use ($search) {
                    $eq->where('title', 'like', "%{$search}%");
                })->orWhereHas('trainer', function ($tq) use ($search) {
                    $tq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $deadlineFilter = (string) $request->get('deadline_filter', 'all');
        if (in_array($deadlineFilter, ['overdue', 'on_time', 'no_deadline'], true)) {
            $pendingEventsQuery->where(function ($q) use ($deadlineFilter) {
                $q->whereHas('event', function ($eq) use ($deadlineFilter) {
                    if ($deadlineFilter === 'overdue') {
                        $eq->whereNotNull('material_deadline')->where('material_deadline', '<', now());
                    } elseif ($deadlineFilter === 'on_time') {
                        $eq->whereNotNull('material_deadline')->where('material_deadline', '>=', now());
                    } else { // no_deadline
                        $eq->whereNull('material_deadline');
                    }
                });
            });
        }

        $pendingEventModules = $pendingEventsQuery
            ->orderByDesc('updated_at')
            ->get();

        // Statistics
        $totalPending = Course::where(function ($q) {
            $q->where('status', 'pending_review')
                ->orWhereHas('modules', fn($mq) => $mq->where('review_status', 'pending_review')->whereNotNull('content_url')->where('content_url', '!=', ''));
        })->count()
            + \App\Models\EventTrainerModule::where('status', 'pending_review')->count();
        $totalApproved = Course::whereIn('status', ['approved', 'active'])->count()
            + \App\Models\EventTrainerModule::where('status', 'approved')->count();
        $totalRejected = Course::where('status', 'rejected')->count()
            + \App\Models\EventTrainerModule::where('status', 'rejected')->count();

        $deadlineMonitoring = $this->buildDeadlineMonitoring($pendingMaterials->getCollection());

        $totalTrainers = User::whereIn('role', ['trainer', 'Trainer'])->count();
        $activeTrainers = User::whereIn('role', ['trainer', 'Trainer'])
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $teachingTrainers = User::whereIn('role', ['trainer', 'Trainer'])
            ->where(function ($q) {
                $q->whereHas('coursesAsTrainer')->orWhereHas('eventsAsTrainer');
            })->count();

        $trainers = User::whereIn('role', ['trainer', 'Trainer'])
            ->withCount(['coursesAsTrainer', 'eventsAsTrainer'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.trainer.material.approvals', compact(
            'pendingMaterials',
            'pendingEventModules',
            'totalPending',
            'totalApproved',
            'totalRejected',
            'deadlineMonitoring',
            'deadlineFilter',
            'totalTrainers',
            'activeTrainers',
            'teachingTrainers',
            'trainers'
        ));
    }

    /**
     * Display specific material for review with preview, grouped by unit (bab)
     */
    public function show(Course $material)
    {
        // Load relationships
        $material->load([
            'trainer',
            'category',
            'modules.quizQuestions',
            'modules.quizQuestions.answers',
            'reviews',
            'units'
        ]);

        $structureCompleteness = $this->assessStructureCompleteness($material);

        $allModules = $material->modules->sortBy('order_no');
        $unitTitles = $material->units->pluck('title', 'unit_no');

        // Grouping logic (consistent with studio: chunk by 3)
        // If course has units, ensure we have at least that many chunks
        $chunks = $allModules->chunk(3)->values();
        $unitCount = max($chunks->count(), $material->units->count());
        $unitSummaries = [];

        for ($index = 0; $index < $unitCount; $index++) {
            $unitModules = $chunks->get($index, collect());
            $unitNo = $index + 1;

            // A module is considered "uploaded/active" if it has a file, description, or is a quiz with questions.
            $uploadedInUnit = $unitModules->filter(
                fn($m) =>
                !empty($m->content_url) ||
                !empty($m->description) ||
                ($m->isQuiz() && $m->quizQuestions->count() > 0)
            );

            $anyPending = $uploadedInUnit->contains('review_status', 'pending_review');
            $anyRejected = $uploadedInUnit->contains('review_status', 'rejected');
            $allApproved = $uploadedInUnit->isNotEmpty() && $uploadedInUnit->every('review_status', 'approved');

            $unitSummaries[] = [
                'unit_no' => $unitNo,
                'unit_label' => "Bab $unitNo" . (isset($unitTitles[$unitNo]) ? ": " . $unitTitles[$unitNo] : ""),
                'total' => $unitModules->count(),
                'uploaded' => $uploadedInUnit->count(),
                'any_pending' => $anyPending,
                'any_rejected' => $anyRejected,
                'all_approved' => $allApproved,
                'modules' => $unitModules
            ];
        }

        $uploadedModules = $allModules->filter(
            fn($m) =>
            !empty($m->content_url) ||
            !empty($m->description) ||
            ($m->isQuiz() && $m->quizQuestions->count() > 0)
        );
        $uploadedModulesCount = $uploadedModules->count();

        $totalTrainers = User::whereIn('role', ['trainer', 'Trainer'])->count();
        $activeTrainers = User::whereIn('role', ['trainer', 'Trainer'])
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $teachingTrainers = User::whereIn('role', ['trainer', 'Trainer'])
            ->where(function ($q) {
                $q->whereHas('coursesAsTrainer')->orWhereHas('eventsAsTrainer');
            })->count();

        return view('admin.trainer.material.show', compact(
            'material',
            'structureCompleteness',
            'unitSummaries',
            'uploadedModulesCount',
            'uploadedModules',
            'totalTrainers',
            'activeTrainers',
            'teachingTrainers'
        ));
    }

    /**
     * Approve a single module
     */
    public function approveModule(Course $material, CourseModule $module)
    {
        if ((int) $module->course_id !== (int) $material->id) {
            abort(404, 'Modul tidak ditemukan pada course ini.');
        }

        $module->update([
            'review_status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'review_rejection_reason' => null,
        ]);

        // Cek apakah semua modul course sudah approved → otomatis approve course
        $allModulesApproved = CourseModule::where('course_id', $material->id)
            ->where(function ($q) {
                $q->where(function ($inner) {
                    $inner->whereNotNull('content_url')
                        ->where('content_url', '!=', '');
                })->orWhere(function ($inner) {
                    $inner->whereNotNull('description')
                        ->where('description', '!=', '');
                })->orWhere(function ($inner) {
                    $inner->where('type', 'quiz')
                        ->whereHas('quizQuestions');
                });
            })
            ->where(function ($q) {
                $q->where('review_status', '!=', 'approved')
                  ->orWhereNull('review_status');
            })
            ->doesntExist();

        // Auto-approve course if all present modules are approved
        $hasAnyApproved = CourseModule::where('course_id', $material->id)
            ->where('review_status', 'approved')
            ->exists();

        if ($allModulesApproved && $hasAnyApproved && $material->status === 'pending_review') {
            $material->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);

            if (!empty($material->trainer_id)) {
                TrainerNotification::create([
                    'trainer_id' => (int) $material->trainer_id,
                    'type' => 'course_material_approved',
                    'title' => 'Semua Materi Course Diterima',
                    'message' => 'Semua materi course "' . $material->name . '" telah disetujui oleh admin trainer.',
                    'data' => [
                        'entity_type' => 'course',
                        'entity_id' => (int) $material->id,
                        'url' => route('trainer.detail-course', $material->id),
                    ],
                    'expires_at' => now()->addDays(30),
                ]);
            }
        }

        return redirect()
            ->route('admin.trainer.material.show', $material)
            ->with('success', 'Modul "' . $module->title . '" berhasil disetujui.');
    }

    /**
     * Reject a single module with reason
     */

    /**
     * Approve all modules in a specific unit (Bab)
     */
    public function approveUnit(Request $request, Course $material)
    {
        $unitNo = (int) $request->input('unit_no');
        if ($unitNo <= 0) {
            return back()->with('error', 'Nomor bab tidak valid.');
        }

        // Each unit consists of 3 modules (chunk by 3)
        $startOrder = ($unitNo - 1) * 3 + 1;
        $endOrder = $unitNo * 3;

        $modules = CourseModule::where('course_id', $material->id)
            ->whereBetween('order_no', [$startOrder, $endOrder])
            ->get();

        if ($modules->isEmpty()) {
            return back()->with('error', "Materi pada Bab {$unitNo} tidak ditemukan.");
        }

        foreach ($modules as $module) {
            // Only approve if it has content (to avoid approving empty slots accidentally)
            $hasContent = $module->type === 'quiz'
                ? ($module->quizQuestions()->count() > 0)
                : (!empty($module->content_url) || !empty($module->description));

            if ($hasContent) {
                $module->update([
                    'review_status' => 'approved',
                    'reviewed_at' => now(),
                    'reviewed_by' => Auth::id(),
                    'review_rejection_reason' => null,
                ]);
            }
        }

        // Trigger course-level approval check
        $allModulesApproved = CourseModule::where('course_id', $material->id)
            ->where(function ($q) {
                $q->where(function ($inner) {
                    $inner->whereNotNull('content_url')
                        ->where('content_url', '!=', '');
                })->orWhere(function ($inner) {
                    $inner->whereNotNull('description')
                        ->where('description', '!=', '');
                })->orWhere(function ($inner) {
                    $inner->where('type', 'quiz')
                        ->whereHas('quizQuestions');
                });
            })
            ->where(function ($q) {
                $q->where('review_status', '!=', 'approved')
                  ->orWhereNull('review_status');
            })
            ->doesntExist();

        // Auto-approve course if all present modules are approved
        $hasAnyApproved = CourseModule::where('course_id', $material->id)
            ->where('review_status', 'approved')
            ->exists();

        if ($allModulesApproved && $hasAnyApproved && $material->status === 'pending_review') {
            $material->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);
        }

        return redirect()
            ->route('admin.trainer.material.show', $material)
            ->with('success', "Seluruh materi pada Bab {$unitNo} yang tersedia telah disetujui.");
    }
    public function rejectModule(Request $request, Course $material, CourseModule $module)
    {
        if ((int) $module->course_id !== (int) $material->id) {
            abort(404, 'Modul tidak ditemukan pada course ini.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ], [
            'rejection_reason.required' => 'Alasan penolakan modul wajib diisi.',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

        $module->update([
            'review_status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'review_rejection_reason' => $request->rejection_reason,
        ]);

        // Kembalikan status course ke pending_review jika sebelumnya approved
        if ($material->status === 'approved') {
            $material->update(['status' => 'pending_review']);
        }

        // Kirim notifikasi revisi ke trainer
        if (!empty($material->trainer_id)) {
            TrainerNotification::create([
                'trainer_id' => (int) $material->trainer_id,
                'type' => 'course_material_rejected',
                'title' => 'Modul Course Perlu Revisi',
                'message' => 'Modul "' . $module->title . '" pada course "' . $material->name . '" perlu revisi. Catatan: ' . $request->rejection_reason,
                'data' => [
                    'entity_type' => 'course',
                    'entity_id' => (int) $material->id,
                    'rejection_reason' => $request->rejection_reason,
                    'url' => route('trainer.courses.studio', $material->id),
                ],
                'expires_at' => now()->addDays(30),
            ]);
        }

        return redirect()
            ->route('admin.trainer.material.show', $material)
            ->with('success', 'Modul "' . $module->title . '" ditolak dan catatan revisi telah dikirim ke trainer.');
    }


    /**
     * Reject all modules within a specific unit (bab) with reason
     */
    public function rejectUnit(Request $request, Course $material, int $unitIndex)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

        $allModules = CourseModule::where('course_id', $material->id)
            ->orderBy('order_no', 'asc')
            ->get();

        $chunks = $allModules->chunk(3)->values();
        $unitModules = $chunks->get($unitIndex, collect());

        if ($unitModules->isEmpty()) {
            return redirect()->route('admin.trainer.material.show', $material)
                ->with('error', 'Unit (bab) tidak ditemukan.');
        }

        $rejectedCount = 0;
        foreach ($unitModules as $module) {
            if ($module->isQuiz())
                continue;
            $hasContent = !empty($module->content_url) || trim((string) ($module->description ?? '')) !== '';
            if (!$hasContent)
                continue;
            $module->update([
                'review_status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'review_rejection_reason' => $request->rejection_reason,
            ]);
            $rejectedCount++;
        }

        // Kembalikan status course ke pending_review
        if ($material->status === 'approved') {
            $material->update(['status' => 'pending_review']);
        }

        // Notifikasi ke trainer
        if (!empty($material->trainer_id)) {
            TrainerNotification::create([
                'trainer_id' => (int) $material->trainer_id,
                'type' => 'course_material_rejected',
                'title' => 'Materi Bab ' . ($unitIndex + 1) . ' Perlu Revisi',
                'message' => 'Materi Bab ' . ($unitIndex + 1) . ' pada course "' . $material->name . '" ditolak. Catatan: ' . $request->rejection_reason,
                'data' => [
                    'entity_type' => 'course',
                    'entity_id' => (int) $material->id,
                    'rejection_reason' => $request->rejection_reason,
                    'url' => route('trainer.courses.studio', $material->id),
                ],
                'expires_at' => now()->addDays(30),
            ]);
        }

        return redirect()
            ->route('admin.trainer.material.show', $material)
            ->with('success', 'Bab ' . ($unitIndex + 1) . ': ' . $rejectedCount . ' modul ditolak. Notifikasi revisi dikirim ke trainer.');
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
     * Approve material
     */
    public function approve(Course $material)
    {
        $material->loadMissing(['trainer', 'modules']);

        if ($material->modules->isEmpty()) {
            return redirect()
                ->route('admin.trainer.material.show', $material)
                ->with('error', 'Belum ada materi yang diupload trainer. Approval hanya bisa dilakukan untuk materi yang sudah ada.');
        }

        // Ensure all modules are marked as approved when bulk approving the course material
        CourseModule::where('course_id', $material->id)
            ->where(function ($q) {
                // 1. Approve modules with content (PDF/Video)
                $q->where(function ($inner) {
                    $inner->whereNotNull('content_url')
                        ->where('content_url', '!=', '');
                })
                    // 2. Approve modules with description (Text module)
                    ->orWhere(function ($inner) {
                    $inner->whereNotNull('description')
                        ->where('description', '!=', '');
                })
                    // 3. Approve quizzes that have questions
                    ->orWhere(function ($inner) {
                    $inner->where('type', 'quiz')
                        ->whereHas('quizQuestions');
                });
            })
            ->update([
                'review_status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
            ]);

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
                'message' => 'Materi course "' . $material->name . '" telah disetujui oleh admin trainer.',
                'data' => [
                    'entity_type' => 'course',
                    'entity_id' => (int) $material->id,
                    'url' => route('trainer.detail-course', $material->id),
                ],
                'expires_at' => now()->addDays(30),
            ]);
        }

        return redirect()
            ->route('admin.trainer.material.approvals')
            ->with('success', "Materi yang sudah diupload pada \"{$material->name}\" berhasil disetujui!");
    }

    /**
     * Revoke course material approval/rejection and set back to pending review
     */
    public function revoke(Request $request, Course $material)
    {
        $moduleId = $request->input('module_id');

        // Case 1: Revoke a specific CourseModule
        if ($moduleId) {
            $module = CourseModule::where('course_id', $material->id)
                ->where('id', $moduleId)
                ->firstOrFail();

            $module->update([
                'review_status' => 'pending_review',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_rejection_reason' => null,
            ]);

            // If the course itself was already approved or rejected, set it back to pending_review
            if (in_array($material->status, ['approved', 'rejected', 'active'], true)) {
                $material->update([
                    'status' => 'pending_review',
                    'approved_at' => null,
                    'approved_by' => null,
                    'rejected_at' => null,
                    'rejection_reason' => null,
                ]);
            }

            try {
                if (!empty($material->trainer_id)) {
                    TrainerNotification::create([
                        'trainer_id' => (int) $material->trainer_id,
                        'type' => 'course_material_revoked',
                        'title' => 'Peninjauan Modul Ditarik',
                        'message' => 'Persetujuan/penolakan untuk modul "' . $module->title . '" pada course "' . $material->name . '" telah ditarik kembali oleh admin trainer. Status kembali ke Peninjauan.',
                        'data' => [
                            'entity_type' => 'course',
                            'entity_id' => (int) $material->id,
                            'url' => route('trainer.courses.studio', $material->id),
                        ],
                        'expires_at' => now()->addDays(30),
                    ]);
                }
            } catch (\Throwable $e) {}

            return back()->with('success', 'Keputusan untuk modul "' . $module->title . '" berhasil dibatalkan. Status dikembalikan ke Menunggu Tinjauan.');
        } 
        // Case 2: Revoke the entire Course Material status
        else {
            // Set all active/uploaded modules back to pending_review
            CourseModule::where('course_id', $material->id)
                ->where(function ($q) {
                    $q->whereNotNull('content_url')
                        ->where('content_url', '!=', '')
                        ->orWhereNotNull('description')
                        ->where('description', '!=', '')
                        ->orWhere(function ($inner) {
                            $inner->where('type', 'quiz')
                                ->whereHas('quizQuestions');
                        });
                })
                ->update([
                    'review_status' => 'pending_review',
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'review_rejection_reason' => null,
                ]);

            $material->update([
                'status' => 'pending_review',
                'approved_at' => null,
                'approved_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);

            try {
                if (!empty($material->trainer_id)) {
                    TrainerNotification::create([
                        'trainer_id' => (int) $material->trainer_id,
                        'type' => 'course_material_revoked',
                        'title' => 'Peninjauan Materi Course Ditarik',
                        'message' => 'Persetujuan/penolakan untuk materi course "' . $material->name . '" telah ditarik kembali oleh admin trainer. Status kembali ke Peninjauan.',
                        'data' => [
                            'entity_type' => 'course',
                            'entity_id' => (int) $material->id,
                            'url' => route('trainer.courses.studio', $material->id),
                        ],
                        'expires_at' => now()->addDays(30),
                    ]);
                }
            } catch (\Throwable $e) {}

            return back()->with('success', 'Keputusan untuk materi course "' . $material->name . '" berhasil dibatalkan. Status dikembalikan ke Menunggu Tinjauan.');
        }
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
                'message' => 'Materi course "' . $material->name . '" perlu revisi. Catatan admin trainer: ' . $rejectionReason,
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
            ->route('admin.trainer.material.approvals')
            ->with('success', "Materi \"{$material->name}\" ditolak dan catatan revisi telah dikirim ke trainer.");
    }

    /**
     * Show all approved materials
     */
    public function approved(Request $request)
    {
        $query = Course::with(['trainer', 'category'])
            ->whereIn('status', ['approved', 'active'])
            ->withCount('modules');

        $approvedEventsQuery = \App\Models\Event::query()
            ->whereHas('trainerModules', function ($q) {
                $q->where('status', 'approved');
            })
            ->with([
                'trainer:id,name,email,avatar',
                'trainerModules' => function ($q) {
                    $q->where('status', 'approved');
                }
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $approvedEventsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('trainer', fn($q) => $q->where('name', 'like', "%{$search}%"));
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

        $approvedMaterials = $query->orderByRaw('CASE WHEN approved_at IS NULL THEN created_at ELSE approved_at END DESC')->paginate(15);

        $approvedEventModules = $approvedEventsQuery
            ->get()
            ->sortByDesc(function ($event) {
                return $event->material_approved_at ?: $event->created_at;
            })
            ->values();

        $deadlineMonitoring = $this->buildDeadlineMonitoring($approvedMaterials->getCollection());

        $totalTrainers = User::whereIn('role', ['trainer', 'Trainer'])->count();
        $activeTrainers = User::whereIn('role', ['trainer', 'Trainer'])
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $teachingTrainers = User::whereIn('role', ['trainer', 'Trainer'])
            ->where(function ($q) {
                $q->whereHas('coursesAsTrainer')->orWhereHas('eventsAsTrainer');
            })->count();

        $trainers = User::whereIn('role', ['trainer', 'Trainer'])
            ->withCount(['coursesAsTrainer', 'eventsAsTrainer'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.trainer.material.approved', compact(
            'approvedMaterials',
            'approvedEventModules',
            'deadlineMonitoring',
            'deadlineFilter',
            'totalTrainers',
            'activeTrainers',
            'teachingTrainers',
            'trainers'
        ));
    }

    /**
     * Show all rejected materials
     */
    public function rejected(Request $request)
    {
        $query = Course::with(['trainer', 'category'])
            ->where('status', 'rejected')
            ->withCount('modules');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('trainer', function ($trainerQuery) use ($search) {
                        $trainerQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $rejectedMaterials = $query
            ->orderByDesc('rejected_at')
            ->orderByDesc('updated_at')
            ->paginate(10)
            ->withQueryString();

        $deadlineFilter = $request->query('deadline_filter', 'all');

        $deadlineMonitoring = $this->buildDeadlineMonitoring($rejectedMaterials->getCollection());

        $rejectedEventsQuery = \App\Models\Event::query()
            ->whereHas('trainerModules', function ($q) {
                $q->where('status', 'rejected');
            })
            ->with([
                'trainer:id,name,email,avatar',
                'trainerModules' => function ($q) {
                    $q->where('status', 'rejected');
                }
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $rejectedEventsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('trainer', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $deadlineFilter = (string) $request->get('deadline_filter', 'all');
        if (in_array($deadlineFilter, ['overdue', 'on_time', 'no_deadline'], true)) {
            $rejectedEventsQuery->where(function ($q) use ($deadlineFilter) {
                if ($deadlineFilter === 'overdue') {
                    $q->whereNotNull('material_deadline')->where('material_deadline', '<', now());
                } elseif ($deadlineFilter === 'on_time') {
                    $q->whereNotNull('material_deadline')->where('material_deadline', '>=', now());
                } else { // no_deadline
                    $q->whereNull('material_deadline');
                }
            });
        }

        $rejectedEventModules = $rejectedEventsQuery
            ->orderByDesc('updated_at')
            ->get();

        $totalPending = Course::where('status', 'pending_review')->count();
        $totalApproved = Course::whereIn('status', ['approved', 'active'])->count();
        $totalRejected = Course::where('status', 'rejected')->count();

        return view('admin.trainer.material.rejected', compact(
            'rejectedMaterials',
            'rejectedEventModules',
            'deadlineMonitoring',
            'deadlineFilter',
            'totalPending',
            'totalApproved',
            'totalRejected'
        ));
    }
}