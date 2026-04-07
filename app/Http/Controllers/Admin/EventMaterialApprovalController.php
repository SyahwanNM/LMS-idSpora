<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TrainerNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventMaterialApprovalController extends Controller
{
    private function parseSpeakerNames(string $speaker): array
    {
        $speaker = trim($speaker);
        if ($speaker === '') {
            return [];
        }

        $parts = preg_split('/\s*[,;]+\s*/', $speaker) ?: [];
        $names = [];
        foreach ($parts as $part) {
            $part = trim((string) $part);
            if ($part !== '') {
                $names[] = mb_strtolower($part);
            }
        }

        return array_values(array_unique($names));
    }

    private function resolveAssignedTrainerIds(Event $event): array
    {
        $ids = [];

        if (!empty($event->trainer_id)) {
            $ids[] = (int) $event->trainer_id;
        }

        $speakerNames = $this->parseSpeakerNames((string) $event->speaker);
        if (!empty($speakerNames)) {
            $speakerMatchedIds = User::query()
                ->where('role', 'trainer')
                ->whereIn('id', function ($query) use ($speakerNames) {
                    $query->select('id')
                        ->from('users')
                        ->whereIn(\DB::raw('LOWER(name)'), $speakerNames);
                })
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->filter(fn($id) => $id > 0)
                ->values()
                ->all();

            $ids = array_merge($ids, $speakerMatchedIds);
        }

        return collect($ids)
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function buildDeadlineMonitoring(Event $event): array
    {
        $materialStatus = (string) ($event->material_status ?? 'draft');
        $isRevisionWindow = $materialStatus === 'rejected';
        $deadline = $isRevisionWindow
            ? ($event->material_revision_deadline ?: $event->start_at?->copy()->subDays(3))
            : ($event->material_deadline ?: $event->start_at?->copy()->subDays(7));

        if (empty($deadline)) {
            return [
                'label' => 'Tanpa tenggat',
                'class' => 'neutral',
                'is_overdue' => false,
            ];
        }

        $deadline = Carbon::parse($deadline);
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
        $query = Event::with(['trainer'])
            ->whereNotNull('module_path')
            ->where(function ($q) {
                $q->where('material_status', 'pending')
                    ->orWhere('material_status', 'pending_review');
            });

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
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
                $query->orderBy('title', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('title', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $pendingMaterials = $query->paginate(10);
        $pendingMaterials->getCollection()->transform(function (Event $event) {
            $event->setAttribute('deadline_monitoring', $this->buildDeadlineMonitoring($event));
            return $event;
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
        if (empty($event->module_path)) {
            return back()->with('error', 'Material event tidak ditemukan.');
        }

        return view('admin.event-material-show', compact('event'));
    }

    /**
     * Approve event material and notify trainer
     */
    public function approve(Request $request, Event $event)
    {
        if (empty($event->module_path)) {
            return back()->with('error', 'Material event tidak ditemukan.');
        }

        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403, 'Hanya admin yang dapat melakukan aksi ini.');
        }

        // Update material status
        $event->update([
            'material_status' => 'approved',
            'material_approved_at' => now(),
            'material_approved_by' => Auth::id(),
            'material_rejection_reason' => null,
        ]);

        // Notify trainer about approval
        $assignedTrainerIds = $this->resolveAssignedTrainerIds($event);
        foreach ($assignedTrainerIds as $trainerId) {
            TrainerNotification::create([
                'trainer_id' => (int) $trainerId,
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

        if (empty($event->module_path)) {
            return back()->with('error', 'Material event tidak ditemukan.');
        }

        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403, 'Hanya admin yang dapat melakukan aksi ini.');
        }

        $rejectionReason = trim((string) $request->input('rejection_reason'));

        // Update material status
        $event->update([
            'material_status' => 'rejected',
            'material_approved_at' => now(),
            'material_approved_by' => Auth::id(),
            'material_rejection_reason' => $rejectionReason,
        ]);

        // Notify trainer about rejection
        $assignedTrainerIds = $this->resolveAssignedTrainerIds($event);
        foreach ($assignedTrainerIds as $trainerId) {
            TrainerNotification::create([
                'trainer_id' => (int) $trainerId,
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
