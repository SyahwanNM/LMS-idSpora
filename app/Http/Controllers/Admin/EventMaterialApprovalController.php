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

    private function syncLegacyEventMaterialsToAssignments(): void
    {
        $legacyEvents = Event::query()
            ->whereNotNull('trainer_id')
            ->whereNotNull('module_path')
            ->get([
                'id',
                'trainer_id',
                'module_path',
                'material_status',
                'module_submitted_at',
                'material_approved_at',
                'material_approved_by',
                'material_rejection_reason',
                'updated_at',
            ]);

        foreach ($legacyEvents as $event) {
            $assignment = TrainerAssignment::query()
                ->where('event_id', (int) $event->id)
                ->where('trainer_id', (int) $event->trainer_id)
                ->orderByDesc('id')
                ->first();

            if ($assignment && !empty($assignment->material_path)) {
                continue;
            }

            $payload = [
                'material_path' => $event->module_path,
                'material_status' => $event->material_status ?: 'pending_review',
                'material_submitted_at' => $event->module_submitted_at ?: $event->updated_at,
                'material_approved_at' => $event->material_approved_at,
                'material_approved_by' => $event->material_approved_by,
                'material_rejection_reason' => $event->material_rejection_reason,
                'status' => $assignment?->status ?: 'accepted',
            ];

            if ($assignment) {
                $assignment->update($payload);
                continue;
            }

            TrainerAssignment::query()->create(array_merge($payload, [
                'event_id' => (int) $event->id,
                'trainer_id' => (int) $event->trainer_id,
            ]));
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
                'label' => 'Overdue',
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

        return view('admin.event-material-approvals', [
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

        $materialPath = $assignment?->material_path ?: $event->module_path;
        if (empty($materialPath)) {
            return back()->with('error', 'Material event tidak ditemukan.');
        }

        $materialStatus = (string) ($assignment?->material_status ?: $event->material_status ?: 'pending_review');
        $materialSubmittedAt = $assignment?->material_submitted_at ?: $event->module_submitted_at ?: $event->updated_at;
        $materialReviewedAt = $assignment?->material_approved_at
            ?: $assignment?->material_rejected_at
            ?: $event->material_approved_at;
        $materialRejectionReason = $assignment?->material_rejection_reason ?: $event->material_rejection_reason;
        $materialTrainer = $assignment?->trainer ?: $event->trainer;

        return view('admin.event-material-show', compact(
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
            abort(403, 'Hanya admin yang dapat mengakses materi event.');
        }

        $assignment = $this->resolveTargetAssignment($event, $request);
        $materialPath = (string) ($assignment?->material_path ?: $event->module_path ?: '');
        if ($materialPath === '') {
            abort(404, 'Material event tidak ditemukan.');
        }

        $absolutePath = $this->resolveReadableMaterialPath($materialPath);
        if (empty($absolutePath)) {
            abort(404, 'File material tidak ditemukan di storage.');
        }

        $filename = basename($materialPath);
        if ((string) $request->query('download', '0') === '1') {
            return response()->download($absolutePath, $filename);
        }

        return response()->file($absolutePath, [
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Approve event material and notify trainer
     */
    public function approve(Request $request, Event $event)
    {
        $assignment = $this->resolveTargetAssignment($event, $request);

        if ($assignment && empty($assignment->material_path)) {
            return back()->with('error', 'Material event tidak ditemukan.');
        }

        if (!$assignment && empty($event->module_path)) {
            return back()->with('error', 'Material event tidak ditemukan.');
        }

        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403, 'Hanya admin yang dapat melakukan aksi ini.');
        }

        if ($assignment) {
            $assignment->update([
                'material_status' => 'approved',
                'material_approved_at' => now(),
                'material_approved_by' => Auth::id(),
                'material_rejected_at' => null,
                'material_rejected_by' => null,
                'material_rejection_reason' => null,
            ]);
        } else {
            // Backward compatibility for legacy event-level material records.
            $event->update([
                'material_status' => 'approved',
                'material_approved_at' => now(),
                'material_approved_by' => Auth::id(),
                'material_rejection_reason' => null,
            ]);
        }

        $targetTrainerId = (int) ($assignment?->trainer_id ?: $event->trainer_id);
        if ($targetTrainerId > 0) {
            TrainerNotification::create([
                'trainer_id' => $targetTrainerId,
                'type' => 'event_material_approved',
                'title' => 'Materi Event Diterima',
                'message' => 'Materi event "' . $event->title . '" telah disetujui oleh admin.',
                'data' => [
                    'entity_type' => 'event',
                    'entity_id' => (int) $event->id,
                    'url' => route('trainer.events.show', $event->id),
                ],
                'expires_at' => now()->addDays(30),
            ]);
        }

        return back()->with('success', 'Materi event berhasil disetujui. Trainer telah diberitahu.');
    }

    /**
     * Reject event material and notify trainer
     */
    public function reject(Request $request, Event $event)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $assignment = $this->resolveTargetAssignment($event, $request);

        if ($assignment && empty($assignment->material_path)) {
            return back()->with('error', 'Material event tidak ditemukan.');
        }

        if (!$assignment && empty($event->module_path)) {
            return back()->with('error', 'Material event tidak ditemukan.');
        }

        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403, 'Hanya admin yang dapat melakukan aksi ini.');
        }

        $rejectionReason = trim((string) $request->input('rejection_reason'));

        if ($assignment) {
            $assignment->update([
                'material_status' => 'rejected',
                'material_rejected_at' => now(),
                'material_rejected_by' => Auth::id(),
                'material_rejection_reason' => $rejectionReason,
            ]);
        } else {
            // Backward compatibility for legacy event-level material records.
            $event->update([
                'material_status' => 'rejected',
                'material_approved_at' => now(),
                'material_approved_by' => Auth::id(),
                'material_rejection_reason' => $rejectionReason,
            ]);
        }

        $targetTrainerId = (int) ($assignment?->trainer_id ?: $event->trainer_id);
        if ($targetTrainerId > 0) {
            TrainerNotification::create([
                'trainer_id' => $targetTrainerId,
                'type' => 'event_material_rejected',
                'title' => 'Materi Event Ditolak',
                'message' => 'Materi event "' . $event->title . '" ditolak. Alasan: ' . $rejectionReason,
                'data' => [
                    'entity_type' => 'event',
                    'entity_id' => (int) $event->id,
                    'url' => route('trainer.events.show', $event->id),
                    'rejection_reason' => $rejectionReason,
                ],
                'expires_at' => now()->addDays(30),
            ]);
        }

        return back()->with('success', 'Materi event berhasil ditolak. Trainer telah diberitahu untuk meng-upload ulang.');
    }
}
