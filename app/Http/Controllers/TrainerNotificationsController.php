<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Event;
use App\Models\TrainerNotification;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\TrainerActivityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerNotificationsController extends Controller
{
    private function trainerSchemes(): array
    {
        return config('trainer_schemes', []);
    }

    public function index(Request $request)
    {
        $uid = Auth::id();
        if (!$uid) {
            return response()->json(['items' => [], 'unread' => 0]);
        }

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

        $entityType = (string) data_get($notification->data, 'entity_type', '');
        $entityId = (int) data_get($notification->data, 'entity_id', 0);
        $invitationStatus = (string) data_get($notification->data, 'invitation_status', 'pending');

        if ($entityType === 'event' && $entityId > 0 && in_array($invitationStatus, ['pending', 'accepted'], true)) {
            return redirect()->to(route('trainer.events.show', $entityId) . '#e-agreement');
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

        $activityService = app(TrainerActivityService::class);
        $trainer = User::query()->findOrFail($uid);
        $activityService->refresh($trainer);

        $entityType = (string) data_get($notification->data, 'entity_type', '');

        $rules = [
            'decision' => 'required|in:accept,reject',
        ];

        if ($entityType === 'course') {
            $rules['contribution_scheme'] = 'required_if:decision,accept|nullable|string';
        }
        $rules['e_agreement'] = 'required_if:decision,accept|accepted';

        $validated = $request->validate($rules);
        $decision = (string) $validated['decision'];

        $data = is_array($notification->data) ? $notification->data : [];
        $currentStatus = (string) data_get($data, 'invitation_status', 'pending');
        if (in_array($currentStatus, ['accepted', 'rejected', 'expired'], true)) {
            return back()->with('success', 'Undangan ini sudah diproses sebelumnya.');
        }

        $dueAtRaw = (string) data_get($data, 'due_at', '');
        if ($dueAtRaw !== '') {
            try {
                $dueAt = Carbon::parse($dueAtRaw);
                if ($currentStatus === 'pending' && $dueAt->isPast()) {
                    $data['invitation_status'] = 'expired';
                    $data['expired_at'] = now()->toIso8601String();
                    $notification->invitation_status = 'expired';
                    $notification->data = $data;
                    $notification->save();

                    $activityService->recordExpiredInvitation($trainer);

                    return back()->with('error', 'Undangan ini sudah expired (melewati SLA 24 jam).');
                }
            } catch (\Throwable $e) {
                // Ignore invalid timestamp and continue using existing flow.
            }
        }

        $entityId = (int) data_get($data, 'entity_id', 0);
        $schemeKey = trim((string) ($validated['contribution_scheme'] ?? ''));
        $schemeMap = $activityService->availableContributionSchemes($trainer);

        if ($decision === 'accept' && !$activityService->canReceiveInvitation($trainer)) {
            return back()->with('error', 'Akun Anda tidak aktif untuk menerima undangan. Aktifkan kembali terlebih dahulu.');
        }

        if ($entityType === 'course' && $decision === 'accept' && ($schemeKey === '' || !array_key_exists($schemeKey, $schemeMap))) {
            return back()->with('error', 'Silakan pilih skema kontribusi course terlebih dahulu.');
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

                    $scheme = $schemeMap[$schemeKey];
                    $course->trainer_contribution_scheme = $schemeKey;
                    $course->trainer_revenue_percent = (int) data_get($scheme, 'percent', 0);
                    $course->trainer_scheme_accepted_at = now();
                    $course->trainer_e_agreement_accepted_at = now();
                    $course->save();
                } else {
                    if ((int) $course->trainer_id === (int) $uid) {
                        $course->trainer_id = null;
                        $course->trainer_contribution_scheme = null;
                        $course->trainer_revenue_percent = null;
                        $course->trainer_scheme_accepted_at = null;
                        $course->trainer_e_agreement_accepted_at = null;
                        $course->save();
                    }
                }
            }
        }

        if ($entityType === 'event' && $entityId > 0) {
            $event = Event::query()->find($entityId);
            if ($event) {
                if ($decision === 'accept') {
                    if (!empty($event->trainer_id) && (int) $event->trainer_id !== (int) $uid) {
                        return back()->with('error', 'Undangan tidak bisa diterima karena event sudah ditugaskan ke trainer lain.');
                    }
                    if ((int) $event->trainer_id !== (int) $uid) {
                        $event->trainer_id = $uid;
                    }
                    $event->material_deadline = now()->addDays(3);
                    $event->save();
                } else {
                    if ((int) $event->trainer_id === (int) $uid) {
                        $event->trainer_id = null;
                        $event->save();
                    }
                }
            }
        }

        $data['invitation_status'] = $decision === 'accept' ? 'accepted' : 'rejected';
        $data['responded_at'] = now()->toIso8601String();
        $data['e_agreement_accepted'] = $decision === 'accept' ? true : false;
        $data['e_agreement_accepted_at'] = $decision === 'accept' ? now()->toIso8601String() : null;
        $data['upload_due_at'] = $decision === 'accept' ? now()->addDays(3)->toIso8601String() : null;
        if ($entityType === 'course' && $decision === 'accept' && $schemeKey !== '') {
            $data['contribution_scheme'] = $schemeKey;
            $data['trainer_revenue_percent'] = (int) data_get($schemeMap[$schemeKey] ?? [], 'percent', 0);
        }
        $notification->invitation_status = $data['invitation_status'];
        $notification->responded_at = now();
        $notification->data = $data;
        if (is_null($notification->read_at)) {
            $notification->read_at = now();
        }
        $notification->save();

        $activityService->resetExpiredInvitationStreak($trainer);

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
                    'contribution_scheme' => $data['contribution_scheme'] ?? null,
                    'trainer_revenue_percent' => $data['trainer_revenue_percent'] ?? null,
                    'source_notification_id' => (int) $notification->id,
                    'url' => $adminUrl,
                ],
                'expires_at' => now()->addDays(14),
            ]);
        }

        if ($decision === 'accept') {
            $target = (string) data_get($data, 'url', '');
            if ($target !== '') {
                return redirect()->to($target);
            }

            if ($entityType === 'event' && $entityId > 0) {
                return redirect()->route('trainer.events.show', $entityId);
            }

            if ($entityType === 'course' && $entityId > 0) {
                return redirect()->route('trainer.detail-course', $entityId);
            }

            return redirect()->route('trainer.dashboard');
        }

        return back()->with('success', 'Undangan berhasil ditolak.');
    }

    /**
     * Accept event invitation with scheme selection (Modal Flow)
     * 
     * POST /trainer/notifications/{id}/accept-with-scheme
     * 
     * Params:
     * - scheme_type: 1|2|3
     * - legal_agreement_1: 1 (checkbox)
     * - legal_agreement_2: 1 (checkbox)
     */
    public function acceptEventWithScheme(Request $request, TrainerNotification $notification)
    {
        $uid = Auth::id();
        if (!$uid || (int) $notification->trainer_id !== (int) $uid) {
            abort(403);
        }

        $validated = $request->validate([
            'scheme_type' => 'required|integer|in:1,2,3',
            'legal_agreement_1' => 'required|accepted',
            'legal_agreement_2' => 'required|accepted',
        ]);

        $activityService = app(TrainerActivityService::class);
        $trainer = User::query()->findOrFail($uid);
        $activityService->refresh($trainer);

        if (!$activityService->canReceiveInvitation($trainer)) {
            return back()->with('error', 'Akun Anda tidak aktif untuk menerima undangan. Aktifkan kembali terlebih dahulu.');
        }

        $data = is_array($notification->data) ? $notification->data : [];
        $entityType = (string) data_get($data, 'entity_type', '');
        $entityId = (int) data_get($data, 'entity_id', 0);
        $currentStatus = (string) data_get($data, 'invitation_status', 'pending');

        if ($entityType !== 'event' || $entityId <= 0) {
            return back()->with('error', 'Tipe undangan tidak valid untuk workflow ini.');
        }

        if (in_array($currentStatus, ['accepted', 'rejected', 'expired'], true)) {
            return back()->with('success', 'Undangan ini sudah diproses sebelumnya.');
        }

        // Check SLA expiry
        $dueAtRaw = (string) data_get($data, 'due_at', '');
        if ($dueAtRaw !== '') {
            try {
                $dueAt = Carbon::parse($dueAtRaw);
                if ($currentStatus === 'pending' && $dueAt->isPast()) {
                    $data['invitation_status'] = 'expired';
                    $data['expired_at'] = now()->toIso8601String();
                    $notification->invitation_status = 'expired';
                    $notification->data = $data;
                    $notification->save();
                    $activityService->recordExpiredInvitation($trainer);
                    return back()->with('error', 'Undangan ini sudah expired (melewati SLA 24 jam).');
                }
            } catch (\Throwable $e) {
                // Continue if parsing fails
            }
        }

        // Load event
        $event = Event::query()->find($entityId);
        if (!$event) {
            return back()->with('error', 'Event tidak ditemukan.');
        }

        $schemeType = (int) $validated['scheme_type'];

        // Create trainer assignment record
        $assignment = \App\Models\TrainerAssignment::create([
            'trainer_id' => $uid,
            'event_id' => $entityId,
            'invitation_notification_id' => $notification->id,
            'scheme_type' => $schemeType,
            'status' => 'accepted',
            'legal_agreement_accepted_at' => now(),
            'legal_agreement_accepted_ip' => $request->ip(),
            'legal_agreement_accepted_user_agent' => $request->userAgent(),
            'sla_upload_deadline' => now()->addHours(72), // 3 days
        ]);

        // Update event with trainer if not already assigned
        if (empty($event->trainer_id) || (int) $event->trainer_id !== (int) $uid) {
            $event->trainer_id = $uid;
            $event->material_deadline = now()->addDays(3);
            $event->save();
        }

        // Update notification
        $data['invitation_status'] = 'accepted';
        $data['responded_at'] = now()->toIso8601String();
        $data['scheme_type'] = $schemeType;
        $data['legal_agreement_accepted_at'] = now()->toIso8601String();
        $data['legal_agreement_accepted_ip'] = $request->ip();
        $data['assignment_id'] = $assignment->id;
        $data['sla_upload_deadline'] = $assignment->sla_upload_deadline->toIso8601String();

        $notification->invitation_status = 'accepted';
        $notification->responded_at = now();
        $notification->data = $data;
        if (is_null($notification->read_at)) {
            $notification->read_at = now();
        }
        $notification->save();

        $activityService->resetExpiredInvitationStreak($trainer);

        // Notify admin
        $adminMessage = $trainer->name . ' menerima undangan event "' . $event->title . '" dengan skema type ' . $schemeType;
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
                    'entity_type' => 'event',
                    'entity_id' => $entityId,
                    'invitation_status' => 'accepted',
                    'responded_at' => $data['responded_at'],
                    'responded_by_trainer_id' => (int) $uid,
                    'scheme_type' => $schemeType,
                    'assignment_id' => $assignment->id,
                    'source_notification_id' => (int) $notification->id,
                    'url' => route('admin.add-event'),
                ],
                'expires_at' => now()->addDays(14),
            ]);
        }

        // Keep the selected scheme available in trainer studio pages for UI locks.
        session([
            'trainer_active_scheme_type' => $schemeType,
            'trainer_active_scheme_set_at' => now()->toIso8601String(),
        ]);

        // Redirect to trainer event detail page
        return redirect()
            ->route('trainer.events.show', $entityId)
            ->with('success', 'Undangan event diterima. Silakan lanjutkan dari halaman detail event.');
    }
}
