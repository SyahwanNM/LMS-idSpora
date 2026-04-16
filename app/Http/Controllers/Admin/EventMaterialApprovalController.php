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
        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403, 'Hanya admin yang dapat melakukan aksi ini.');
        }

        $moduleId = $request->input('module_id');

        if ($moduleId) {
            $module = \App\Models\EventTrainerModule::where('id', $moduleId)
                ->where('event_id', $event->id)
                ->firstOrFail();

            $module->update([
                'status'           => 'approved',
                'reviewed_by'      => Auth::id(),
                'reviewed_at'      => now(),
                'rejection_reason' => null,
            ]);

            try {
                TrainerNotification::create([
                    'trainer_id' => $module->trainer_id,
                    'type'       => 'event_material_approved',
                    'title'      => 'Materi Event Diterima',
                    'message'    => 'Modul "' . $module->original_name . '" untuk event "' . $event->title . '" telah disetujui.',
                    'data'       => [
                        'entity_type' => 'event',
                        'entity_id'   => (int) $event->id,
                        'url'         => route('trainer.events.show', $event->id),
                    ],
                    'expires_at' => now()->addDays(30),
                ]);
            } catch (\Throwable $e) {}
        } else {
            // Approve all pending for this event
            $pending = \App\Models\EventTrainerModule::where('event_id', $event->id)
                ->where('status', 'pending_review')->get();

            if ($pending->isEmpty()) {
                return back()->with('error', 'Tidak ada materi yang menunggu verifikasi.');
            }

            foreach ($pending as $module) {
                $module->update([
                    'status'      => 'approved',
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                    'rejection_reason' => null,
                ]);
                try {
                    TrainerNotification::create([
                        'trainer_id' => $module->trainer_id,
                        'type'       => 'event_material_approved',
                        'title'      => 'Materi Event Diterima',
                        'message'    => 'Modul "' . $module->original_name . '" untuk event "' . $event->title . '" telah disetujui.',
                        'data'       => ['entity_type' => 'event', 'entity_id' => (int) $event->id, 'url' => route('trainer.events.show', $event->id)],
                        'expires_at' => now()->addDays(30),
                    ]);
                } catch (\Throwable $e) {}
            }
        }

        // Update event-level status if no more pending
        $stillPending = \App\Models\EventTrainerModule::where('event_id', $event->id)
            ->where('status', 'pending_review')->count();
        if ($stillPending === 0) {
            $event->update([
                'material_status'           => 'approved',
                'material_approved_at'      => now(),
                'material_approved_by'      => Auth::id(),
                'material_rejection_reason' => null,
                'module_verified_at'        => now(),
                'module_verified_by'        => Auth::id(),
            ]);
        }

        return back()->with('success', 'Materi event berhasil disetujui. Trainer telah diberitahu.');
    }

    /**
     * Reject event material and notify trainer
     */
    public function reject(Request $request, Event $event)
    {
        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403, 'Hanya admin yang dapat melakukan aksi ini.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $rejectionReason = trim((string) $request->input('reason'));
        $moduleId = $request->input('module_id');

        if ($moduleId) {
            $module = \App\Models\EventTrainerModule::where('id', $moduleId)
                ->where('event_id', $event->id)
                ->firstOrFail();

            $module->update([
                'status'           => 'rejected',
                'reviewed_by'      => Auth::id(),
                'reviewed_at'      => now(),
                'rejection_reason' => $rejectionReason,
            ]);

            try {
                TrainerNotification::create([
                    'trainer_id' => $module->trainer_id,
                    'type'       => 'event_material_rejected',
                    'title'      => 'Materi Event Ditolak',
                    'message'    => 'Modul "' . $module->original_name . '" untuk event "' . $event->title . '" ditolak. Alasan: ' . $rejectionReason,
                    'data'       => [
                        'entity_type'      => 'event',
                        'entity_id'        => (int) $event->id,
                        'url'              => route('trainer.events.show', $event->id),
                        'rejection_reason' => $rejectionReason,
                    ],
                    'expires_at' => now()->addDays(30),
                ]);
            } catch (\Throwable $e) {}
        } else {
            $pending = \App\Models\EventTrainerModule::where('event_id', $event->id)
                ->where('status', 'pending_review')->get();

            foreach ($pending as $module) {
                $module->update([
                    'status'           => 'rejected',
                    'reviewed_by'      => Auth::id(),
                    'reviewed_at'      => now(),
                    'rejection_reason' => $rejectionReason,
                ]);
                try {
                    TrainerNotification::create([
                        'trainer_id' => $module->trainer_id,
                        'type'       => 'event_material_rejected',
                        'title'      => 'Materi Event Ditolak',
                        'message'    => 'Modul "' . $module->original_name . '" untuk event "' . $event->title . '" ditolak. Alasan: ' . $rejectionReason,
                        'data'       => ['entity_type' => 'event', 'entity_id' => (int) $event->id, 'url' => route('trainer.events.show', $event->id), 'rejection_reason' => $rejectionReason],
                        'expires_at' => now()->addDays(30),
                    ]);
                } catch (\Throwable $e) {}
            }

            $event->update([
                'material_status'           => 'rejected',
                'material_rejection_reason' => $rejectionReason,
                'module_rejected_at'        => now(),
                'module_rejected_by'        => Auth::id(),
                'module_rejection_reason'   => $rejectionReason,
            ]);
        }

        return back()->with('success', 'Materi event berhasil ditolak. Trainer telah diberitahu untuk meng-upload ulang.');
    }
}
