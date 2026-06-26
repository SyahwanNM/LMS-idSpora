<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TrainerAssignment;
use App\Models\TrainerNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EventMaterialApprovalController extends Controller
{
    private function resolveReadableMaterialPath(string $materialPath): ?string
    {
        $raw = trim($materialPath);
        if ($raw === '') {
            return null;
        }

        $candidates = [
            ltrim($raw, '/'),
            ltrim(preg_replace('#^storage/#', '', $raw), '/'),
            ltrim(preg_replace('#^public/#', '', $raw), '/'),
        ];

        foreach (array_unique($candidates) as $candidate) {
            if ($candidate === '') {
                continue;
            }

            if (Storage::disk('public')->exists($candidate)) {
                return Storage::disk('public')->path($candidate);
            }
        }

        return null;
    }

    private function stampLogo(string $materialPath): void
    {
        $absolutePath = $this->resolveReadableMaterialPath($materialPath);
        if (!$absolutePath) {
            \Illuminate\Support\Facades\Log::warning('stampLogo failed to resolve readable path for: ' . $materialPath);
            return;
        }

        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        if (!in_array($extension, ['pdf', 'pptx', 'png', 'jpg', 'jpeg'], true)) {
            \Illuminate\Support\Facades\Log::warning('stampLogo skipped due to unsupported extension: ' . $extension);
            return;
        }

        // Backup the original file if it hasn't been backed up yet
        $backupPath = $absolutePath . '.original';
        if (!file_exists($backupPath)) {
            copy($absolutePath, $backupPath);
            \Illuminate\Support\Facades\Log::info('Created original backup at: ' . $backupPath);
        }

        $logoPath = public_path('aset/logo idspora_dark.png');
        if (!file_exists($logoPath)) {
            \Illuminate\Support\Facades\Log::warning('stampLogo skipped because logo file was not found at: ' . $logoPath);
            return;
        }

        $pythonScript = app_path('Scripts/stamp_logo.py');
        if (!file_exists($pythonScript)) {
            \Illuminate\Support\Facades\Log::warning('stampLogo skipped because python script was not found at: ' . $pythonScript);
            return;
        }

        // Call the python script using escapeshellarg for safety
        $command = sprintf(
            'python %s --file %s --logo %s',
            escapeshellarg($pythonScript),
            escapeshellarg($absolutePath),
            escapeshellarg($logoPath)
        );

        $output = [];
        $resultCode = 0;
        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            \Illuminate\Support\Facades\Log::error('Logo stamping failed for: ' . $absolutePath . '. Output: ' . implode("\n", $output));
        } else {
            \Illuminate\Support\Facades\Log::info('stampLogo command finished with code 0. Output: ' . implode("\n", $output));
        }
    }

    private function restoreOriginal(string $materialPath): void
    {
        $absolutePath = $this->resolveReadableMaterialPath($materialPath);
        if (!$absolutePath) {
            return;
        }

        $backupPath = $absolutePath . '.original';
        if (file_exists($backupPath)) {
            copy($backupPath, $absolutePath);
            unlink($backupPath);
            \Illuminate\Support\Facades\Log::info('Restored original file and removed backup: ' . $backupPath);
        }
    }

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

    private function resolveTargetAssignment(Event $event, Request $request): ?TrainerAssignment
    {
        $assignmentId = (int) ($request->query('assignment_id') ?? $request->input('assignment_id') ?? 0);
        if ($assignmentId > 0) {
            return TrainerAssignment::query()
                ->with(['trainer:id,name,email,avatar', 'event:id,title,event_date,event_time,location,material_deadline'])
                ->where('event_id', (int) $event->id)
                ->where('id', $assignmentId)
                ->first();
        }

        return TrainerAssignment::query()
            ->with(['trainer:id,name,email,avatar', 'event:id,title,event_date,event_time,location,material_deadline'])
            ->where('event_id', (int) $event->id)
            ->whereNotNull('material_path')
            ->orderByDesc('material_submitted_at')
            ->orderByDesc('updated_at')
            ->first();
    }

    private function buildDeadlineMonitoring(Event $event): array
    {
        if (empty($event->material_deadline)) {
            return [
                'label' => 'Tanpa tenggat',
                'class' => 'neutral',
                'is_overdue' => false,
            ];
        }

        $deadline = Carbon::parse($event->material_deadline);
        $now = now();

        if ($now->gt($deadline)) {
            return [
                'label' => 'Lewat Tenggat',
                'class' => 'overdue',
                'is_overdue' => true,
            ];
        }

        $daysLeft = (int) ceil($now->diffInMinutes($deadline) / (60 * 24));

        if ($daysLeft <= 1) {
            return [
                'label' => 'H-1',
                'class' => 'urgent',
                'is_overdue' => false,
            ];
        }

        if ($daysLeft <= 2) {
            return [
                'label' => 'H-2',
                'class' => 'warning',
                'is_overdue' => false,
            ];
        }

        return [
            'label' => 'H-' . $daysLeft,
            'class' => 'safe',
            'is_overdue' => false,
        ];
    }

    /**
     * Display queue of event materials pending review
     */
    public function index(Request $request)
    {
        $this->syncLegacyEventMaterialsToAssignments();

        $query = TrainerAssignment::query()
            ->with([
                'event:id,title,event_date,event_time,location,material_deadline',
                'trainer:id,name,email,avatar',
            ])
            ->whereHas('event')
            ->whereNotNull('material_path')
            ->where(function ($q) {
                $q->whereNull('material_status')
                    ->orWhereIn('material_status', ['pending', 'pending_review']);
            });

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('event', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })->orWhereHas('trainer', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Sort functionality
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('material_submitted_at', 'asc')
                    ->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->join('events', 'events.id', '=', 'trainer_assignments.event_id')
                    ->orderBy('events.title', 'asc')
                    ->select('trainer_assignments.*');
                break;
            case 'name_desc':
                $query->join('events', 'events.id', '=', 'trainer_assignments.event_id')
                    ->orderBy('events.title', 'desc')
                    ->select('trainer_assignments.*');
                break;
            default: // newest
                $query->orderByDesc('material_submitted_at')
                    ->orderByDesc('created_at');
                break;
        }

        $pendingMaterials = $query->paginate(10);
        $pendingMaterials->getCollection()->transform(function (TrainerAssignment $assignment) {
            $event = $assignment->event;
            if ($event) {
                $assignment->setAttribute('deadline_monitoring', $this->buildDeadlineMonitoring($event));
            }

            if (empty($assignment->material_submitted_at) && !empty($assignment->material_path)) {
                $assignment->setAttribute('material_submitted_at', $assignment->updated_at);
            }

            return $assignment;
        });

        return view('admin.trainer.material.event-material-approvals', [
            'materials' => $pendingMaterials,
            'sort' => $sort,
        ]);
    }

    /**
     * Show details of a specific event material
     */
    public function show(Request $request, Event $event)
    {
        $assignment = $this->resolveTargetAssignment($event, $request);
        $targetTrainerId = $assignment?->trainer_id ?: $event->trainer_id;

        $event->load([
            'trainerModules' => function ($q) use ($targetTrainerId) {
                $q->where('trainer_id', $targetTrainerId);
            },
            'trainerModules.trainer',
            'trainerModules.reviewer',
            'trainer'
        ]);

        $materialPath = $assignment?->material_path ?: $event->module_path;
        if ($event->trainerModules->isEmpty() && empty($materialPath)) {
            return back()->with('error', 'Material event tidak ditemukan.');
        }

        $materialStatus = (string) ($assignment?->material_status ?: $event->material_status ?: 'pending_review');
        $materialSubmittedAt = $assignment?->material_submitted_at ?: $event->module_submitted_at ?: $event->updated_at;
        $materialReviewedAt = $assignment?->material_approved_at
            ?: $assignment?->material_rejected_at
            ?: $event->material_approved_at;
        $materialRejectionReason = $assignment?->material_rejection_reason ?: $event->material_rejection_reason;
        $materialTrainer = $assignment?->trainer ?: $event->trainer;

        return view('admin.trainer.material.event-material-show', compact(
            'event',
            'assignment',
            'materialPath',
            'materialStatus',
            'materialSubmittedAt',
            'materialReviewedAt',
            'materialRejectionReason',
            'materialTrainer'
        ));
    }

    /**
     * Stream event material for admin preview/download
     */
    public function stream(Request $request, Event $event): BinaryFileResponse
    {
        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403, 'Hanya admin trainer yang dapat mengakses materi event.');
        }

        $moduleId = (int) $request->query('module_id', 0);
        $materialPath = '';

        if ($moduleId > 0) {
            $module = \App\Models\EventTrainerModule::query()
                ->where('event_id', $event->id)
                ->where('id', $moduleId)
                ->firstOrFail();
            $materialPath = (string) ($module->path ?? '');
            if ($materialPath === '' || preg_match('#^https?://#i', $materialPath)) {
                abort(404, 'File modul tidak ditemukan.');
            }
        } else {
            $assignment = $this->resolveTargetAssignment($event, $request);
            $materialPath = (string) ($assignment?->material_path ?: $event->module_path ?: '');
        }

        if ($materialPath === '') {
            abort(404, 'Material event tidak ditemukan.');
        }

        $absolutePath = $this->resolveReadableMaterialPath($materialPath);
        if (empty($absolutePath)) {
            abort(404, 'File material tidak ditemukan di storage.');
        }

        $filename = basename($materialPath);
        $headers = [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        if ((string) $request->query('download', '0') === '1') {
            return response()->download($absolutePath, $filename, $headers);
        }

        return response()->file($absolutePath, array_merge([
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ], $headers));
    }

    private function syncEventMaterialStatus(int $eventId, int $trainerId): void
    {
        $event = Event::find($eventId);
        if (!$event) {
            return;
        }

        // Count module statuses
        $modules = \App\Models\EventTrainerModule::where('event_id', $eventId)
            ->where('trainer_id', $trainerId)
            ->get();

        $totalModules = $modules->count();
        $approvedModules = $modules->where('status', 'approved')->count();
        $rejectedModules = $modules->where('status', 'rejected')->count();
        $pendingModules = $modules->whereIn('status', ['pending_review', 'pending'])->count();

        // Determine assignment status
        $assignment = TrainerAssignment::where('event_id', $eventId)
            ->where('trainer_id', $trainerId)
            ->first();

        $newStatus = 'pending_review';

        if ($totalModules === 0) {
            // If no modules, check if assignment has material_path
            if ($assignment && !empty($assignment->material_path)) {
                $newStatus = 'pending_review';
            } else {
                $newStatus = 'pending';
            }
        } elseif ($pendingModules > 0) {
            $newStatus = 'pending_review';
        } elseif ($approvedModules === $totalModules) {
            $newStatus = 'approved';
        } elseif ($rejectedModules > 0) {
            $newStatus = 'rejected';
        }

        if ($assignment) {
            $payload = [
                'material_status' => $newStatus,
            ];

            if ($newStatus === 'approved') {
                $payload['material_approved_at'] = now();
                $payload['material_approved_by'] = Auth::id();
                $payload['material_rejection_reason'] = null;
            } elseif ($newStatus === 'rejected') {
                if (empty($assignment->material_rejection_reason)) {
                    $firstRejected = $modules->where('status', 'rejected')->first();
                    $payload['material_rejection_reason'] = $firstRejected?->rejection_reason;
                }
                $payload['material_rejected_at'] = now();
                $payload['material_rejected_by'] = Auth::id();
            } else { // pending_review / pending
                $payload['material_approved_at'] = null;
                $payload['material_approved_by'] = null;
                $payload['material_rejected_at'] = null;
                $payload['material_rejected_by'] = null;
                $payload['material_rejection_reason'] = null;
            }

            $assignment->update($payload);
        }

        // Synchronize with Event if this trainer is the primary trainer
        if ((int) $event->trainer_id === (int) $trainerId) {
            $eventPayload = [
                'material_status' => $newStatus,
            ];

            if ($newStatus === 'approved') {
                $eventPayload['material_approved_at'] = now();
                $eventPayload['material_approved_by'] = Auth::id();
                $eventPayload['material_rejection_reason'] = null;
                $eventPayload['module_verified_at'] = now();
                $eventPayload['module_verified_by'] = Auth::id();
            } elseif ($newStatus === 'rejected') {
                if (empty($event->material_rejection_reason)) {
                    $firstRejected = $modules->where('status', 'rejected')->first();
                    $eventPayload['material_rejection_reason'] = $firstRejected?->rejection_reason;
                }
                $eventPayload['material_approved_at'] = null;
                $eventPayload['material_approved_by'] = null;
                $eventPayload['module_verified_at'] = null;
                $eventPayload['module_verified_by'] = null;
            } else { // pending_review / pending
                $eventPayload['material_approved_at'] = null;
                $eventPayload['material_approved_by'] = null;
                $eventPayload['material_rejection_reason'] = null;
                $eventPayload['module_verified_at'] = null;
                $eventPayload['module_verified_by'] = null;
            }

            $event->update($eventPayload);
        }
    }

    /**
     * Approve event material and notify trainer
     */
    public function approve(Request $request, Event $event)
    {
        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403);
        }

        $moduleId = $request->input('module_id');
        $assignmentId = $request->input('assignment_id');
        $stampLogo = $request->boolean('stamp_logo');

        // Case 1: Approval for a specific EventTrainerModule
        if ($moduleId) {
            $etm = \App\Models\EventTrainerModule::where('id', $moduleId)
                ->where('event_id', $event->id)
                ->firstOrFail();

            if ($stampLogo && !empty($etm->path)) {
                $this->stampLogo($etm->path);
            }

            $etm->update([
                'status'           => 'approved',
                'logo_stamped'     => $stampLogo && !empty($etm->path),
                'reviewed_by'      => Auth::id(),
                'reviewed_at'      => now(),
                'rejection_reason' => null,
            ]);

            $this->syncEventMaterialStatus($event->id, $etm->trainer_id);

            try {
                TrainerNotification::create([
                    'trainer_id' => $etm->trainer_id,
                    'type'       => 'event_material_approved',
                    'title'      => 'Materi Event Diterima',
                    'message'    => 'Modul "' . $etm->original_name . '" untuk event "' . $event->title . '" telah disetujui.',
                    'data'       => ['entity_type' => 'event', 'entity_id' => (int) $event->id, 'url' => route('trainer.events.show', $event->id)],
                    'expires_at' => now()->addDays(30),
                ]);
            } catch (\Throwable $e) {}
        } 
        // Case 2: Approval for a specific TrainerAssignment
        elseif ($assignmentId) {
            $assignment = \App\Models\TrainerAssignment::where('id', $assignmentId)
                ->where('event_id', $event->id)
                ->firstOrFail();

            if ($stampLogo) {
                // Fetch pending modules before bulk update
                $pendingModules = \App\Models\EventTrainerModule::where('event_id', $event->id)
                    ->where('trainer_id', $assignment->trainer_id)
                    ->where('status', '!=', 'approved')
                    ->get();
                foreach ($pendingModules as $etm) {
                    if (!empty($etm->path)) {
                        $this->stampLogo($etm->path);
                        $etm->update(['logo_stamped' => true]);
                    }
                }
                if (!empty($assignment->material_path)) {
                    $this->stampLogo($assignment->material_path);
                }
            }

            $assignment->update([
                'material_status'           => 'approved',
                'logo_stamped'              => $stampLogo && !empty($assignment->material_path),
                'material_approved_at'      => now(),
                'material_approved_by'      => Auth::id(),
                'material_rejection_reason' => null,
            ]);

            // Also approve all modules associated with this trainer for this event
            \App\Models\EventTrainerModule::where('event_id', $event->id)
                ->where('trainer_id', $assignment->trainer_id)
                ->where('status', '!=', 'approved')
                ->update([
                    'status'           => 'approved',
                    'logo_stamped'     => $stampLogo,
                    'reviewed_by'      => Auth::id(),
                    'reviewed_at'      => now(),
                    'rejection_reason' => null,
                ]);

            $this->syncEventMaterialStatus($event->id, $assignment->trainer_id);

            try {
                TrainerNotification::create([
                    'trainer_id' => $assignment->trainer_id,
                    'type'       => 'event_material_approved',
                    'title'      => 'Materi Event Diterima',
                    'message'    => 'Materi untuk event "' . $event->title . '" telah disetujui.',
                    'data'       => ['entity_type' => 'event', 'entity_id' => (int) $event->id, 'url' => route('trainer.events.show', $event->id)],
                    'expires_at' => now()->addDays(30),
                ]);
            } catch (\Throwable $e) {}
        }
        // Case 3: Fallback - Approve whatever material is currently associated with the event (Legacy)
        else {
            if ($stampLogo) {
                $pendingModules = \App\Models\EventTrainerModule::where('event_id', $event->id)
                    ->where('status', '!=', 'approved')
                    ->get();
                foreach ($pendingModules as $etm) {
                    if (!empty($etm->path)) {
                        $this->stampLogo($etm->path);
                        $etm->update(['logo_stamped' => true]);
                    }
                }
                if (!empty($event->module_path)) {
                    $this->stampLogo($event->module_path);
                }
            }

            $event->update([
                'material_status'           => 'approved',
                'material_approved_at'      => now(),
                'material_approved_by'      => Auth::id(),
                'material_rejection_reason' => null,
                'module_verified_at'        => now(),
                'module_verified_by'        => Auth::id(),
            ]);

            // Also approve all modules associated with this event
            \App\Models\EventTrainerModule::where('event_id', $event->id)
                ->where('status', '!=', 'approved')
                ->update([
                    'status'           => 'approved',
                    'logo_stamped'     => $stampLogo,
                    'reviewed_by'      => Auth::id(),
                    'reviewed_at'      => now(),
                    'rejection_reason' => null,
                ]);

            // Also sync back to assignment if exists for the primary trainer
            if ($event->trainer_id) {
                \App\Models\TrainerAssignment::where('event_id', $event->id)
                    ->where('trainer_id', $event->trainer_id)
                    ->update([
                        'material_status'      => 'approved',
                        'logo_stamped'         => $stampLogo,
                        'material_approved_at' => now(),
                        'material_approved_by' => Auth::id(),
                    ]);
                
                try {
                    TrainerNotification::create([
                        'trainer_id' => $event->trainer_id,
                        'type'       => 'event_material_approved',
                        'title'      => 'Materi Event Diterima',
                        'message'    => 'Materi untuk event "' . $event->title . '" telah disetujui.',
                        'data'       => ['entity_type' => 'event', 'entity_id' => (int) $event->id, 'url' => route('trainer.events.show', $event->id)],
                        'expires_at' => now()->addDays(30),
                    ]);
                } catch (\Throwable $e) {}
            }
        }

        return back()->with('success', 'Materi event berhasil disetujui. Trainer telah diberitahu.');
    }

    /**
     * Reject event material and notify trainer
     */
    public function reject(Request $request, Event $event)
    {
        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403);
        }

        $request->validate([
            'reason' => 'required_without:rejection_reason|nullable|string|max:500',
            'rejection_reason' => 'required_without:reason|nullable|string|max:500',
        ]);

        $rejectionReason = trim((string) $request->input('rejection_reason', $request->input('reason')));
        $moduleId = $request->input('module_id');
        $assignmentId = $request->input('assignment_id');

        if ($moduleId) {
            $etm = \App\Models\EventTrainerModule::where('id', $moduleId)
                ->where('event_id', $event->id)
                ->firstOrFail();

            $etm->update([
                'status'           => 'rejected',
                'reviewed_by'      => Auth::id(),
                'reviewed_at'      => now(),
                'rejection_reason' => $rejectionReason,
            ]);

            $this->syncEventMaterialStatus($event->id, $etm->trainer_id);

            try {
                TrainerNotification::create([
                    'trainer_id' => $etm->trainer_id,
                    'type'       => 'event_material_rejected',
                    'title'      => 'Materi Event Ditolak',
                    'message'    => 'Modul "' . $etm->original_name . '" untuk event "' . $event->title . '" ditolak. Alasan: ' . $rejectionReason,
                    'data'       => ['entity_type' => 'event', 'entity_id' => (int) $event->id, 'url' => route('trainer.events.show', $event->id), 'rejection_reason' => $rejectionReason],
                    'expires_at' => now()->addDays(30),
                ]);
            } catch (\Throwable $e) {}
        } elseif ($assignmentId) {
            $assignment = \App\Models\TrainerAssignment::where('id', $assignmentId)
                ->where('event_id', $event->id)
                ->firstOrFail();

            $assignment->update([
                'material_status'           => 'rejected',
                'material_rejected_at'      => now(),
                'material_rejected_by'      => Auth::id(),
                'material_rejection_reason' => $rejectionReason,
            ]);

            // Also reject all modules associated with this trainer for this event
            \App\Models\EventTrainerModule::where('event_id', $event->id)
                ->where('trainer_id', $assignment->trainer_id)
                ->where('status', '!=', 'rejected')
                ->update([
                    'status'           => 'rejected',
                    'reviewed_by'      => Auth::id(),
                    'reviewed_at'      => now(),
                    'rejection_reason' => $rejectionReason,
                ]);

            $this->syncEventMaterialStatus($event->id, $assignment->trainer_id);

            try {
                TrainerNotification::create([
                    'trainer_id' => $assignment->trainer_id,
                    'type'       => 'event_material_rejected',
                    'title'      => 'Materi Event Ditolak',
                    'message'    => 'Materi untuk event "' . $event->title . '" ditolak. Alasan: ' . $rejectionReason,
                    'data'       => ['entity_type' => 'event', 'entity_id' => (int) $event->id, 'url' => route('trainer.events.show', $event->id), 'rejection_reason' => $rejectionReason],
                    'expires_at' => now()->addDays(30),
                ]);
            } catch (\Throwable $e) {}
        } else {
            // Fallback Legacy
            $event->update([
                'material_status'           => 'rejected',
                'material_rejection_reason' => $rejectionReason,
            ]);

            // Also reject all modules associated with this event
            \App\Models\EventTrainerModule::where('event_id', $event->id)
                ->where('status', '!=', 'rejected')
                ->update([
                    'status'           => 'rejected',
                    'reviewed_by'      => Auth::id(),
                    'reviewed_at'      => now(),
                    'rejection_reason' => $rejectionReason,
                ]);

            if ($event->trainer_id) {
                \App\Models\TrainerAssignment::where('event_id', $event->id)
                    ->where('trainer_id', $event->trainer_id)
                    ->update([
                        'material_status'           => 'rejected',
                        'material_rejected_at'      => now(),
                        'material_rejection_reason' => $rejectionReason,
                    ]);

                try {
                    TrainerNotification::create([
                        'trainer_id' => $event->trainer_id,
                        'type'       => 'event_material_rejected',
                        'title'      => 'Materi Event Ditolak',
                        'message'    => 'Materi untuk event "' . $event->title . '" ditolak. Alasan: ' . $rejectionReason,
                        'data'       => ['entity_type' => 'event', 'entity_id' => (int) $event->id, 'url' => route('trainer.events.show', $event->id), 'rejection_reason' => $rejectionReason],
                        'expires_at' => now()->addDays(30),
                    ]);
                } catch (\Throwable $e) {}
            }
        }

        return back()->with('success', 'Materi event berhasil ditolak. Trainer telah diberitahu.');
    }

    /**
     * Revoke event material approval/rejection and set back to pending review
     */
    public function revoke(Request $request, Event $event)
    {
        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403);
        }

        $moduleId = $request->input('module_id');
        $assignmentId = $request->input('assignment_id');

        if ($moduleId) {
            $etm = \App\Models\EventTrainerModule::where('id', $moduleId)
                ->where('event_id', $event->id)
                ->firstOrFail();

            if (!empty($etm->path)) {
                $this->restoreOriginal($etm->path);
            }

            $etm->update([
                'status'           => 'pending_review',
                'logo_stamped'     => false,
                'reviewed_by'      => null,
                'reviewed_at'      => null,
                'rejection_reason' => null,
            ]);

            $this->syncEventMaterialStatus($event->id, $etm->trainer_id);
            
            try {
                TrainerNotification::create([
                    'trainer_id' => $etm->trainer_id,
                    'type'       => 'event_material_revoked',
                    'title'      => 'Peninjauan Materi Ditarik',
                    'message'    => 'Persetujuan/penolakan untuk modul "' . $etm->original_name . '" untuk event "' . $event->title . '" telah ditarik kembali oleh admin trainer. Status kembali ke Peninjauan.',
                    'data'       => ['entity_type' => 'event', 'entity_id' => (int) $event->id, 'url' => route('trainer.events.show', $event->id)],
                    'expires_at' => now()->addDays(30),
                ]);
            } catch (\Throwable $e) {}
            
            return back()->with('success', 'Keputusan untuk modul berhasil dibatalkan. Status dikembalikan ke Menunggu Tinjauan.');
        } elseif ($assignmentId) {
            $assignment = \App\Models\TrainerAssignment::where('id', $assignmentId)
                ->where('event_id', $event->id)
                ->firstOrFail();

            // Set all modules associated with this trainer for this event back to pending_review
            $modules = \App\Models\EventTrainerModule::where('event_id', $event->id)
                ->where('trainer_id', $assignment->trainer_id)
                ->get();
            foreach ($modules as $etm) {
                if (!empty($etm->path)) {
                    $this->restoreOriginal($etm->path);
                }
                $etm->update([
                    'status'           => 'pending_review',
                    'logo_stamped'     => false,
                    'reviewed_by'      => null,
                    'reviewed_at'      => null,
                    'rejection_reason' => null,
                ]);
            }

            if (!empty($assignment->material_path)) {
                $this->restoreOriginal($assignment->material_path);
            }

            $assignment->update([
                'material_status'           => 'pending_review',
                'logo_stamped'              => false,
                'material_approved_at'      => null,
                'material_approved_by'      => null,
                'material_rejected_at'      => null,
                'material_rejected_by'      => null,
                'material_rejection_reason' => null,
            ]);

            $this->syncEventMaterialStatus($event->id, $assignment->trainer_id);

            try {
                TrainerNotification::create([
                    'trainer_id' => $assignment->trainer_id,
                    'type'       => 'event_material_revoked',
                    'title'      => 'Peninjauan Materi Ditarik',
                    'message'    => 'Persetujuan/penolakan materi untuk event "' . $event->title . '" telah ditarik kembali oleh admin trainer. Status kembali ke Peninjauan.',
                    'data'       => ['entity_type' => 'event', 'entity_id' => (int) $event->id, 'url' => route('trainer.events.show', $event->id)],
                    'expires_at' => now()->addDays(30),
                ]);
            } catch (\Throwable $e) {}

            return back()->with('success', 'Keputusan materi event berhasil dibatalkan. Status dikembalikan ke Menunggu Tinjauan.');
        } else {
            // Fallback Legacy
            if ($event->trainer_id) {
                // If there's an assignment
                $assignment = \App\Models\TrainerAssignment::where('event_id', $event->id)
                    ->where('trainer_id', $event->trainer_id)
                    ->first();
                
                if ($assignment) {
                    $modules = \App\Models\EventTrainerModule::where('event_id', $event->id)
                        ->where('trainer_id', $assignment->trainer_id)
                        ->get();
                    foreach ($modules as $etm) {
                        if (!empty($etm->path)) {
                            $this->restoreOriginal($etm->path);
                        }
                        $etm->update([
                            'status'           => 'pending_review',
                            'logo_stamped'     => false,
                            'reviewed_by'      => null,
                            'reviewed_at'      => null,
                            'rejection_reason' => null,
                        ]);
                    }
                    if (!empty($assignment->material_path)) {
                        $this->restoreOriginal($assignment->material_path);
                    }
                    $assignment->update([
                        'material_status'      => 'pending_review',
                        'logo_stamped'         => false,
                        'material_approved_at' => null,
                        'material_approved_by' => null,
                        'material_rejected_at' => null,
                        'material_rejected_by' => null,
                    ]);
                    $this->syncEventMaterialStatus($event->id, $assignment->trainer_id);
                } else {
                    if (!empty($event->module_path)) {
                        $this->restoreOriginal($event->module_path);
                    }
                    $event->update([
                        'material_status'           => 'pending_review',
                        'logo_stamped'              => false,
                        'material_approved_at'      => null,
                        'material_approved_by'      => null,
                        'material_rejection_reason' => null,
                        'module_verified_at'        => null,
                        'module_verified_by'        => null,
                    ]);
                }
            } else {
                if (!empty($event->module_path)) {
                    $this->restoreOriginal($event->module_path);
                }
                $event->update([
                    'material_status'           => 'pending_review',
                    'logo_stamped'              => false,
                    'material_approved_at'      => null,
                    'material_approved_by'      => null,
                    'material_rejection_reason' => null,
                    'module_verified_at'        => null,
                    'module_verified_by'        => null,
                ]);
            }

            return back()->with('success', 'Keputusan materi event berhasil dibatalkan. Status dikembalikan ke Menunggu Tinjauan.');
        }
    }
}
