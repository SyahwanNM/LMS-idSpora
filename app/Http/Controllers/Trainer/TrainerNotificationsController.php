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
                ->limit(50)
                ->get()
                ->filter(function (TrainerNotification $notification) {
                    $type = (string) data_get($notification->data, 'entity_type', '');
                    $id = (int) data_get($notification->data, 'entity_id', 0);
                    if ($type === 'event' || $notification->type === 'event_invitation') {
                        return $id > 0 && Event::where('id', $id)->exists();
                    } elseif ($type === 'course' || $notification->type === 'course_invitation') {
                        return $id > 0 && Course::where('id', $id)->exists();
                    }
                    return true;
                })
                ->take(15)
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
                ->get()
                ->filter(function (TrainerNotification $notification) {
                    $type = (string) data_get($notification->data, 'entity_type', '');
                    $id = (int) data_get($notification->data, 'entity_id', 0);
                    if ($type === 'event' || $notification->type === 'event_invitation') {
                        return $id > 0 && Event::where('id', $id)->exists();
                    } elseif ($type === 'course' || $notification->type === 'course_invitation') {
                        return $id > 0 && Course::where('id', $id)->exists();
                    }
                    return true;
                })
                ->count();

            return response()->json([
                'items' => $items,
                'unread' => $unread,
            ]);
        }

        $invitationCollection = TrainerNotification::where('trainer_id', $uid)
            ->whereIn('type', ['course_invitation', 'event_invitation'])
            ->orderByRaw('CASE WHEN read_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('created_at')
            ->get()
            ->filter(function (TrainerNotification $notification) {
                $type = (string) data_get($notification->data, 'entity_type', '');
                $id = (int) data_get($notification->data, 'entity_id', 0);
                if ($type === 'event' || $notification->type === 'event_invitation') {
                    return $id > 0 && Event::where('id', $id)->exists();
                } elseif ($type === 'course' || $notification->type === 'course_invitation') {
                    return $id > 0 && Course::where('id', $id)->exists();
                }
                return true;
            });

        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 15;
        $invitations = new \Illuminate\Pagination\LengthAwarePaginator(
            $invitationCollection->forPage($page, $perPage)->values(),
            $invitationCollection->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        $viewHtml = <<<'HTML'
@extends('layouts.trainer')

@section('title', 'Daftar Undangan')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap');

    .invitation-page-container {
        font-family: 'Outfit', sans-serif;
        max-width: 1000px;
        margin: 0 auto;
        padding-bottom: 80px;
    }
    
    .invitation-header-section {
        background: linear-gradient(135deg, #2e2050 0%, #4a326c 100%);
        padding: 32px;
        border-radius: 24px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(46, 32, 80, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .invitation-header-title {
        font-size: 28px;
        font-weight: 800;
        margin: 0 0 6px 0;
        letter-spacing: -0.5px;
    }

    .invitation-header-desc {
        color: rgba(255, 255, 255, 0.8);
        font-size: 14px;
        margin: 0;
        font-weight: 400;
    }

    .btn-back-dash {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        backdrop-filter: blur(8px);
        text-decoration: none;
    }

    .btn-back-dash:hover {
        background: white;
        color: #2e2050;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
    }

    .invitation-card {
        border: 1px solid #f1f5f9;
        border-radius: 20px;
        background: #ffffff;
        padding: 28px;
        margin-bottom: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .invitation-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: transparent;
        transition: background-color 0.3s ease;
    }

    .invitation-card.is-unread {
        background: #fdfcff;
        border-color: rgba(99, 102, 241, 0.12);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.05), 0 8px 10px -6px rgba(99, 102, 241, 0.05);
    }

    .invitation-card.is-unread::before {
        background-color: #6366f1;
    }

    .invitation-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.06), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: rgba(46, 32, 80, 0.08);
    }

    .invitation-badge-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
    }

    .invitation-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 30px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        letter-spacing: 0.5px;
    }

    .invitation-badge-event {
        background: #f3e8ff;
        color: #6b21a8;
        border: 1px solid #e9d5ff;
    }

    .invitation-badge-course {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .badge-new {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        font-size: 9px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 30px;
        letter-spacing: 0.5px;
    }

    .invitation-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 12px;
    }

    .invitation-title {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.3;
        letter-spacing: -0.2px;
    }

    .invitation-date {
        font-size: 12px;
        color: #94a3b8;
        font-weight: 600;
        white-space: nowrap;
        background: #f8fafc;
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid #f1f5f9;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .invitation-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 13px;
        color: #475569;
        margin-bottom: 18px;
        padding: 12px 0;
        border-top: 1px dashed #f1f5f9;
        border-bottom: 1px dashed #f1f5f9;
    }

    .invitation-meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        padding: 6px 12px;
        border-radius: 10px;
        border: 1px solid #f1f5f9;
        font-weight: 500;
    }

    .invitation-meta i {
        color: #64748b;
        font-size: 14px;
    }

    .invitation-desc {
        font-size: 14.5px;
        color: #475569;
        line-height: 1.6;
        margin-bottom: 22px;
    }

    .invitation-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }

    .btn-action-primary {
        background: linear-gradient(135deg, #2e2050 0%, #4a326c 100%);
        color: white !important;
        padding: 10px 24px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 13px;
        box-shadow: 0 4px 15px rgba(46, 32, 80, 0.15);
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
    }

    .btn-action-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 32, 80, 0.25);
        opacity: 0.95;
    }

    .btn-action-outline {
        border: 1.5px solid #e2e8f0;
        color: #475569 !important;
        background: white;
        padding: 9px 22px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-action-outline:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        color: #0f172a !important;
        transform: translateY(-2px);
    }

    .btn-action-danger-outline {
        border: 1.5px solid #fee2e2;
        color: #dc2626 !important;
        background: white;
        padding: 9px 22px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-action-danger-outline:hover {
        border-color: #dc2626;
        background: #fef2f2;
        transform: translateY(-2px);
    }

    .status-badge-container {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 13px;
        gap: 6px;
    }

    .status-badge-accepted {
        background: #d1fae5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .status-badge-rejected {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .empty-invitations-card {
        padding: 60px 20px;
        text-align: center;
        background: white;
        border-radius: 24px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }

    .empty-icon-wrapper {
        width: 80px;
        height: 80px;
        background: #f3f0f7;
        color: #624388;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px auto;
        font-size: 36px;
    }

    /* Custom responsive tweaks */
    @media (max-width: 768px) {
        .invitation-header-section {
            padding: 24px;
            border-radius: 16px;
            text-align: center;
            justify-content: center;
        }
        .btn-back-dash {
            width: 100%;
            justify-content: center;
        }
        .invitation-card {
            padding: 20px;
            border-radius: 16px;
        }
        .invitation-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        .invitation-date {
            align-self: flex-start;
        }
        .invitation-meta {
            gap: 10px;
        }
        .invitation-meta span {
            width: 100%;
        }
        .invitation-actions {
            flex-direction: column;
            width: 100%;
        }
        .invitation-actions > * {
            width: 100%;
        }
        .btn-action-primary, .btn-action-outline, .btn-action-danger-outline {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="invitation-page-container py-4">
    <div class="invitation-header-section">
        <div>
            <h1 class="invitation-header-title">Daftar Undangan</h1>
            <p class="invitation-header-desc">Kelola semua undangan kelas dan event yang dikirimkan kepada Anda.</p>
        </div>
        <a href="{{ route('trainer.dashboard') }}" class="btn-back-dash">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
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
                <div class="invitation-card {{ is_null($invite->read_at) ? 'is-unread' : '' }}">
                    <div class="invitation-header">
                        <div>
                            <div class="invitation-badge-row">
                                <span class="invitation-badge {{ $inviteEntityType === 'event' ? 'invitation-badge-event' : 'invitation-badge-course' }}">
                                    <i class="bi {{ $inviteEntityType === 'event' ? 'bi-calendar-event' : 'bi-book-half' }} me-1"></i> {{ $inviteTypeLabel }}
                                </span>
                                @if(is_null($invite->read_at))
                                    <span class="badge-new">BARU</span>
                                @endif
                            </div>
                            <h3 class="invitation-title">{{ $invite->title }}</h3>
                        </div>
                        <span class="invitation-date"><i class="bi bi-clock-history me-1"></i> {{ optional($invite->created_at)->diffForHumans() }}</span>
                    </div>

                    <div class="invitation-meta mt-2">
                        <span><i class="bi bi-calendar3"></i> {{ $entityDate }}</span>
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
                                <a href="{{ route('trainer.events.show', $entityId) }}" class="btn-action-outline">
                                    <i class="bi bi-eye"></i> Lihat Detail
                                </a>
                            @elseif($inviteEntityType === 'course' && $entityId > 0)
                                <a href="{{ route('trainer.detail-course', $entityId) }}" class="btn-action-outline">
                                    <i class="bi bi-eye"></i> Lihat Detail
                                </a>
                            @endif
                            
                            @if($inviteEntityType === 'course')
                                <button type="button" class="btn-action-primary" onclick="openSchemeSelectionModal({{ $invite->id }}, '{{ addslashes($invite->title) }}', '{{ $inviteEntityType }}')">
                                    <i class="bi bi-check-circle"></i> Terima Undangan
                                </button>
                            @else
                                <form method="POST" action="{{ route('trainer.notifications.respond', $invite->id) }}" style="margin:0; display:inline-block;">
                                    @csrf
                                    <input type="hidden" name="decision" value="accept">
                                    <button type="submit" class="btn-action-primary">
                                        <i class="bi bi-check-circle"></i> Terima Undangan
                                    </button>
                                </form>
                            @endif

                            <form method="POST" class="js-invitation-response-form" data-confirm="Apakah Anda yakin ingin menolak undangan ini?" action="{{ route('trainer.notifications.respond', $invite->id) }}" style="margin:0; display:inline-block;">
                                @csrf
                                <input type="hidden" name="decision" value="reject">
                                <button type="submit" class="btn-action-danger-outline" data-loading-text="Memproses...">
                                    <i class="bi bi-x-circle"></i> Tolak
                                </button>
                            </form>
                        @elseif($status === 'accepted')
                            <div class="status-badge-container status-badge-accepted">
                                <i class="bi bi-check-circle-fill"></i> Undangan Diterima
                            </div>
                            @if($inviteEntityType === 'event' && $entityId > 0)
                                <a href="{{ route('trainer.events.show', $entityId) }}" class="btn-action-outline ms-2">
                                    Masuk ke Detail Event <i class="bi bi-arrow-right"></i>
                                </a>
                            @elseif($inviteEntityType === 'course' && $entityId > 0)
                                <a href="{{ route('trainer.courses.studio', $entityId) }}" class="btn-action-outline ms-2">
                                    Masuk ke Studio Kelas <i class="bi bi-arrow-right"></i>
                                </a>
                            @endif
                        @elseif($status === 'rejected')
                            <div class="status-badge-container status-badge-rejected">
                                <i class="bi bi-x-circle-fill"></i> Undangan Ditolak
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-invitations-card">
                    <div class="empty-icon-wrapper">
                        <i class="bi bi-envelope-open"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2" style="font-size: 20px;">Tidak Ada Undangan</h4>
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
        $entityType = (string) data_get($data, 'entity_type', '');
        $entityId = (int) data_get($data, 'entity_id', 0);

        $needsSchemeSetup = false;
        if ($entityType === 'course' && $entityId > 0) {
            $course = Course::query()->find($entityId);
            if ($course && empty($course->trainer_contribution_scheme)) {
                $needsSchemeSetup = true;
            }
        }

        if (!$needsSchemeSetup && in_array($currentStatus, ['accepted', 'rejected'], true)) {
            return back()->with('success', 'Undangan ini sudah diproses sebelumnya.');
        }

        if ($entityType === 'course' && $entityId > 0) {
            $course = Course::query()->find($entityId);
            if ($course) {
                if ($decision === 'accept') {
                    if (!empty($course->trainer_id) && (int) $course->trainer_id !== (int) $uid) {
                        return back()->with('error', 'Undangan tidak bisa diterima karena course sudah ditugaskan ke trainer lain.');
                    }
                    if ((int) $course->trainer_id !== (int) $uid) {
                        $course->trainer_id = $uid;
                    }

                    // Save scheme info to course
                    $schemeType = (int) $request->input('scheme_type', 0);
                    $contribScheme = $request->input('contribution_scheme');

                    if ($schemeType === 0 && !empty($contribScheme)) {
                        $schemeType = match ($contribScheme) {
                            'e2e' => 1,
                            'module_video' => 2,
                            'video_only' => 3,
                            default => 0,
                        };
                    } elseif (!empty($schemeType) && empty($contribScheme)) {
                        $contribScheme = match ($schemeType) {
                            1 => 'e2e',
                            2 => 'module_video',
                            3 => 'video_only',
                            default => 'e2e',
                        };
                    }

                    if (empty($contribScheme)) {
                        $contribScheme = 'e2e';
                        $schemeType = 1;
                    }

                    $revenuePercent = match ($contribScheme) {
                        'e2e' => 35,
                        'module_video' => 25,
                        'video_only' => 10,
                        default => 35
                    };

                    $course->trainer_contribution_scheme = $contribScheme;
                    $course->trainer_revenue_percent = $revenuePercent;
                    $course->trainer_scheme_accepted_at = now();
                    $course->save();
                } else {
                    if ((int) $course->trainer_id === (int) $uid) {
                        $course->trainer_id = null;
                        $course->trainer_contribution_scheme = null;
                        $course->trainer_revenue_percent = null;
                        $course->trainer_scheme_accepted_at = null;
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
