<?php

namespace App\Http\Controllers;

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

        $decision = (string) $request->validate([
            'decision' => 'required|in:accept,reject',
        ])['decision'];

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
                    // Multi-speaker support: don't block others if trainer_id is already set.
                    // Only update event->trainer_id if it's currently empty, to record at least one official trainer.
                    if (empty($event->trainer_id)) {
                        $event->trainer_id = $uid;
                        $event->save();
                    }
                } else {
                    // Only clear trainer_id if the current user was the one assigned.
                    if ((int) $event->trainer_id === (int) $uid) {
                        $event->trainer_id = null;
                        $event->save();
                    }
                }
            }
        }

        $data['invitation_status'] = $decision === 'accept' ? 'accepted' : 'rejected';
        $data['responded_at'] = now()->toIso8601String();
        $notification->data = $data;
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
        $notification->read_at = now();
        $notification->save();

        return back()->with('success', 'Undangan berhasil diterima dengan skema yang dipilih.');
    }
}
