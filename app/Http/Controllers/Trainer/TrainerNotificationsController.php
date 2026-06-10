<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Event;
use App\Models\TrainerNotification;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerNotificationsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['items' => [], 'unread' => 0]);
            }
            return redirect()->route('login');
        }

        try {
            app(TrainerController::class)->ensureEventInvitationsExistForTrainer($user);
        } catch (\Exception $e) {
            // Log or ignore gracefully to avoid blocking notifications loading
            \Illuminate\Support\Facades\Log::error('Failed to sync event invitations for trainer: ' . $e->getMessage());
        }

        $uid = $user->id;

        if ($request->expectsJson() || $request->ajax()) {
            $items = TrainerNotification::where('trainer_id', $uid)
                ->orderByDesc('created_at')
                ->limit(15)
                ->get()
                ->map(function (TrainerNotification $notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'type' => $notification->type,
                        'time_ago' => optional($notification->created_at)->diffForHumans(),
                        'url' => data_get($notification->data, 'url'),
                        'read_at' => optional($notification->read_at)?->toIso8601String(),
                    ];
                })
                ->values();

            $unread = TrainerNotification::where('trainer_id', $uid)
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'items' => $items,
                'unread' => $unread,
            ]);
        }

        $invitations = TrainerNotification::where('trainer_id', $uid)
            ->whereIn('type', ['course_invitation', 'event_invitation'])
            ->orderByRaw('CASE WHEN read_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('created_at')
            ->paginate(15);

        $viewHtml = <<<'HTML'
@extends('layouts.trainer')

@section('title', 'Daftar Undangan')

@push('styles')
<style>
    .invitation-page-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    .invitation-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        padding: 24px;
        margin-bottom: 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }
    .invitation-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .invitation-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        display: inline-block;
        margin-bottom: 12px;
    }
    .invitation-badge-event {
        background: #f3e8ff;
        color: #6b21a8;
        border: 1px solid #d8b4fe;
    }
    .invitation-badge-course {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }
    .invitation-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }
    .invitation-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .invitation-date {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }
    .invitation-meta {
        display: flex;
        gap: 20px;
        font-size: 13px;
        color: #475569;
        margin-bottom: 16px;
    }
    .invitation-meta i {
        color: #94a3b8;
        margin-right: 6px;
    }
    .invitation-desc {
        font-size: 14px;
        color: #475569;
        line-height: 1.6;
        margin-bottom: 20px;
    }
    .invitation-actions {
        display: flex;
        gap: 12px;
    }
    .status-text {
        font-weight: 700;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .status-accepted {
        color: #059669;
    }
    .status-rejected {
        color: #dc2626;
    }
</style>
@endpush

@section('content')
<div class="invitation-page-container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Daftar Undangan</h1>
            <p class="text-muted mb-0">Kelola semua undangan kelas dan event yang dikirimkan kepada Anda.</p>
        </div>
        <a href="{{ route('trainer.dashboard') }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="row">
        <div class="col-12">
            @forelse($invitations as $invite)
                @php
                    $inviteEntityType = data_get($invite->data, 'entity_type', 'course');
                    $inviteTypeLabel = $inviteEntityType === 'event' ? 'Event' : 'Course';
                    $entityId = (int) data_get($invite->data, 'entity_id', 0);
                    $entityDate = '-';
                    $entityTime = '-';
                    $entityLocation = '-';
                    
                    if ($inviteEntityType === 'event') {
                        $eventObj = \App\Models\Event::find($entityId);
                        if ($eventObj) {
                            $entityDate = $eventObj->event_date ? $eventObj->event_date->format('d M Y') : 'Jadwal Menyusul';
                            $entityTime = $eventObj->event_time ? \Carbon\Carbon::parse($eventObj->event_time)->format('H:i') : '-';
                            $entityLocation = $eventObj->location ?: ($eventObj->is_online ? 'Online (Virtual)' : '-');
                        }
                    } else {
                        $courseObj = \App\Models\Course::find($entityId);
                        if ($courseObj) {
                             $entityDate = $courseObj->created_at ? $courseObj->created_at->format('d M Y') : '-';
                             $entityLocation = '-';
                        }
                    }
                    
                    $status = (string) data_get($invite->data, 'invitation_status', ($invite->invitation_status ?? 'pending'));
                @endphp
                <div class="invitation-card shadow-sm">
                    <div class="invitation-header">
                        <div>
                            <span class="invitation-badge {{ $inviteEntityType === 'event' ? 'invitation-badge-event' : 'invitation-badge-course' }}">
                                {{ $inviteTypeLabel }}
                            </span>
                            <h3 class="invitation-title">{{ $invite->title }}</h3>
                        </div>
                        <span class="invitation-date">{{ optional($invite->created_at)->diffForHumans() }}</span>
                    </div>

                    <div class="invitation-meta mt-2">
                        <span><i class="bi bi-calendar"></i> {{ $entityDate }}</span>
                        @if($entityTime !== '-')
                            <span><i class="bi bi-clock"></i> {{ $entityTime }}</span>
                        @endif
                        @if($entityLocation !== '-')
                            <span><i class="bi bi-geo-alt"></i> {{ $entityLocation }}</span>
                        @endif
                    </div>

                    <div class="invitation-desc">
                        {{ $invite->message }}
                    </div>

                    <div class="invitation-actions">
                        @if($status === 'pending')
                            @if($inviteEntityType === 'event' && $entityId > 0)
                                <a href="{{ route('trainer.events.show', $entityId) }}" class="btn btn-sm btn-outline-secondary px-4 py-2" style="border-radius: 8px;">
                                    Lihat Detail
                                </a>
                            @elseif($inviteEntityType === 'course' && $entityId > 0)
                                <a href="{{ route('trainer.detail-course', $entityId) }}" class="btn btn-sm btn-outline-secondary px-4 py-2" style="border-radius: 8px;">
                                    Lihat Detail
                                </a>
                            @endif
                            
                            <button type="button" class="btn btn-sm btn-primary px-4 py-2" style="border-radius: 8px; background-color: #624388; border: none;" onclick="openSchemeSelectionModal({{ $invite->id }}, '{{ addslashes($invite->title) }}', '{{ $inviteEntityType }}')">
                                Terima Undangan
                            </button>

                            <form method="POST" action="{{ route('trainer.notifications.respond', $invite->id) }}" style="margin:0;">
                                @csrf
                                <input type="hidden" name="decision" value="reject">
                                <button type="submit" class="btn btn-sm btn-outline-danger px-4 py-2" style="border-radius: 8px;" onclick="return confirm('Apakah Anda yakin ingin menolak undangan ini?');">
                                    Tolak
                                </button>
                            </form>
                        @elseif($status === 'accepted')
                            <span class="status-text status-accepted">
                                <i class="bi bi-check-circle-fill"></i> Undangan Diterima
                            </span>
                            @if($inviteEntityType === 'event' && $entityId > 0)
                                <a href="{{ route('trainer.events.show', $entityId) }}" class="btn btn-sm btn-outline-primary ms-3" style="border-radius: 8px;">
                                    Masuk ke Detail Event
                                </a>
                            @elseif($inviteEntityType === 'course' && $entityId > 0)
                                <a href="{{ route('trainer.courses.studio', $entityId) }}" class="btn btn-sm btn-outline-primary ms-3" style="border-radius: 8px;">
                                    Masuk ke Studio Kelas
                                </a>
                            @endif
                        @elseif($status === 'rejected')
                            <span class="status-text status-rejected">
                                <i class="bi bi-x-circle-fill"></i> Undangan Ditolak
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card p-5 text-center border-0 shadow-sm rounded-4">
                    <div class="text-muted mb-3"><i class="bi bi-envelope-open" style="font-size: 48px;"></i></div>
                    <h4 class="fw-bold text-dark">Tidak Ada Undangan</h4>
                    <p class="text-muted mb-0">Saat ini Anda tidak memiliki undangan kelas atau event baru maupun lama.</p>
                </div>
            @endforelse

            <div class="d-flex justify-content-center mt-4">
                {!! $invitations->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

@include('trainer.partials.scheme-selection-modal')
@endsection
HTML;

        return \Illuminate\Support\Facades\Blade::render($viewHtml, ['invitations' => $invitations]);
    }

    public function markAllRead(Request $request)
    {
        $uid = Auth::id();
        if (!$uid) {
            return $request->expectsJson()
                ? response()->json(['ok' => true])
                : back();
        }

        TrainerNotification::where('trainer_id', $uid)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Semua notifikasi trainer telah dibaca.');
    }

    public function open(TrainerNotification $notification)
    {
        $uid = Auth::id();
        if (!$uid || (int) $notification->trainer_id !== (int) $uid) {
            abort(403);
        }

        if (is_null($notification->read_at)) {
            $notification->read_at = now();
            $notification->save();
        }

        $target = (string) data_get($notification->data, 'url', '');
        if ($target === '') {
            return redirect()->route('trainer.dashboard');
        }

        return redirect()->to($target);
    }

    public function respond(Request $request, TrainerNotification $notification)
    {
        $uid = Auth::id();
        if (!$uid || (int) $notification->trainer_id !== (int) $uid) {
            abort(403);
        }

        $validated = $request->validate([
            'decision' => 'required|in:accept,reject',
            'scheme_type' => 'nullable|integer|in:1,2,3',
            'contribution_scheme' => 'nullable|string',
        ]);
        $decision = $validated['decision'];

        $data = is_array($notification->data) ? $notification->data : [];
        $currentStatus = (string) data_get($data, 'invitation_status', 'pending');
        if (in_array($currentStatus, ['accepted', 'rejected'], true)) {
            return back()->with('success', 'Undangan ini sudah diproses sebelumnya.');
        }

        $entityType = (string) data_get($data, 'entity_type', '');
        $entityId = (int) data_get($data, 'entity_id', 0);

        if ($entityType === 'course' && $entityId > 0) {
            $course = Course::query()->find($entityId);
            if ($course) {
                if ($decision === 'accept') {
                    if (!empty($course->trainer_id) && (int) $course->trainer_id !== (int) $uid) {
                        return back()->with('error', 'Undangan tidak bisa diterima karena course sudah ditugaskan ke trainer lain.');
                    }
                    if ((int) $course->trainer_id !== (int) $uid) {
                        $course->trainer_id = $uid;
                        $course->save();
                    }
                } else {
                    if ((int) $course->trainer_id === (int) $uid) {
                        $course->trainer_id = null;
                        $course->save();
                    }
                }
            }
        }

        if ($entityType === 'event' && $entityId > 0) {
            $event = Event::query()->find($entityId);
            if ($event) {
                if ($decision === 'accept') {
                    // Multi-speaker support: ensure assignment exists and update it
                    \App\Models\TrainerAssignment::updateOrCreate(
                        ['trainer_id' => $uid, 'event_id' => $entityId],
                        [
                            'status' => 'accepted',
                            'invitation_notification_id' => $notification->id,
                            'sla_upload_deadline' => $event->material_deadline ?: now()->addDays(3),
                        ]
                    );

                    // Only update event->trainer_id if it's currently empty, to record at least one official trainer.
                    if (empty($event->trainer_id)) {
                        $event->trainer_id = $uid;
                        $event->save();
                    }
                } else {
                    // Update TrainerAssignment status to rejected
                    \App\Models\TrainerAssignment::updateOrCreate(
                        ['trainer_id' => $uid, 'event_id' => $entityId],
                        [
                            'status' => 'rejected',
                            'invitation_notification_id' => $notification->id,
                        ]
                    );

                    // Only clear trainer_id if the current user was the one assigned.
                    if ((int) $event->trainer_id === (int) $uid) {
                        $event->trainer_id = null;
                        $event->save();
                    }
                }
            }
        }

        $data['invitation_status'] = $decision === 'accept' ? 'accepted' : 'rejected';
        if (isset($validated['scheme_type'])) {
            $data['scheme_type'] = $validated['scheme_type'];
        }
        if (isset($validated['contribution_scheme'])) {
            $data['contribution_scheme'] = $validated['contribution_scheme'];
        }
        $data['responded_at'] = now()->toIso8601String();
        $notification->data = $data;
        $notification->invitation_status = $data['invitation_status'];
        $notification->responded_at = now();
        if (is_null($notification->read_at)) {
            $notification->read_at = now();
        }
        $notification->save();

        $entityLabel = match ($entityType) {
            'event' => 'event',
            'course' => 'course',
            default => 'penugasan',
        };

        $entityTitle = '';
        if ($entityType === 'course' && $entityId > 0) {
            $entityTitle = (string) (Course::query()->whereKey($entityId)->value('name') ?? '');
        }
        if ($entityType === 'event' && $entityId > 0) {
            $entityTitle = (string) (Event::query()->whereKey($entityId)->value('title') ?? '');
        }

        $decisionLabel = $decision === 'accept' ? 'menerima' : 'menolak';
        $trainerName = (string) (Auth::user()?->name ?? ('Trainer #' . (int) $uid));
        $adminMessage = $trainerName . ' ' . $decisionLabel . ' undangan ' . $entityLabel;
        if ($entityTitle !== '') {
            $adminMessage .= ' "' . $entityTitle . '"';
        }
        $adminMessage .= '.';

        $adminUrl = $entityType === 'event'
            ? route('admin.add-event')
            : route('admin.courses.index');

        $adminIds = User::query()
            ->where('role', 'admin')
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->values();

        foreach ($adminIds as $adminId) {
            UserNotification::create([
                'user_id' => $adminId,
                'type' => 'trainer_invitation_response',
                'title' => 'Respons Undangan Trainer',
                'message' => $adminMessage,
                'data' => [
                    'entity_type' => $entityType,
                    'entity_id' => $entityId,
                    'invitation_status' => $data['invitation_status'],
                    'responded_at' => $data['responded_at'],
                    'responded_by_trainer_id' => (int) $uid,
                    'source_notification_id' => (int) $notification->id,
                    'url' => $adminUrl,
                ],
                'expires_at' => now()->addDays(14),
            ]);
        }

        $message = $decision === 'accept'
            ? 'Undangan berhasil diterima.'
            : 'Undangan berhasil ditolak.';

        if ($decision === 'accept' && $entityType === 'course' && $entityId > 0) {
            return redirect()->route('trainer.courses.studio', $entityId)->with('success', $message);
        }

        if ($decision === 'accept' && $entityType === 'event' && $entityId > 0) {
            return redirect()->route('trainer.events.show', $entityId)->with('success', $message);
        }

        return back()->with('success', $message);
    }

    public function acceptWithScheme(Request $request, TrainerNotification $notification)
    {
        $uid = Auth::id();
        if (!$uid || (int) $notification->trainer_id !== (int) $uid) {
            abort(403);
        }

        $validated = $request->validate([
            'scheme_type' => 'required|integer|in:1,2,3',
            'legal_agreement_1' => 'required|in:1',
            'legal_agreement_2' => 'required|in:1',
        ]);

        $data = is_array($notification->data) ? $notification->data : [];
        $entityType = (string) data_get($data, 'entity_type', '');
        $entityId = (int) data_get($data, 'entity_id', 0);

        if ($entityType === 'event' && $entityId > 0) {
            $event = Event::find($entityId);
            if ($event) {
                // Multi-speaker support: ensure assignment exists and update it
                $assignment = \App\Models\TrainerAssignment::updateOrCreate(
                    ['trainer_id' => $uid, 'event_id' => $entityId],
                    [
                        'scheme_type' => $validated['scheme_type'],
                        'status' => 'accepted',
                        'invitation_notification_id' => $notification->id,
                        'sla_upload_deadline' => $event->material_deadline ?: now()->addDays(3),
                    ]
                );

                if (empty($event->trainer_id)) {
                    $event->trainer_id = $uid;
                    $event->save();
                }
            }
        }

        // Update notification status
        $data['invitation_status'] = 'accepted';
        $data['scheme_type'] = $validated['scheme_type'];
        $data['responded_at'] = now()->toIso8601String();
        $notification->data = $data;
        $notification->invitation_status = 'accepted';
        $notification->responded_at = now();
        $notification->read_at = now();
        $notification->save();

        if ($entityType === 'event' && $entityId > 0) {
            return redirect()->route('trainer.events.show', $entityId)
                ->with('success', 'Undangan berhasil diterima dengan skema yang dipilih.');
        }

        return back()->with('success', 'Undangan berhasil diterima dengan skema yang dipilih.');
    }
}
