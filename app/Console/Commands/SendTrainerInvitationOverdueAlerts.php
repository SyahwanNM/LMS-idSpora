<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Event;
use App\Models\TrainerNotification;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\TrainerActivityService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendTrainerInvitationOverdueAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainer:send-invitation-overdue-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send overdue escalation alerts for pending trainer invitations to trainer and admins';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();

        $invitationNotifications = TrainerNotification::query()
            ->whereIn('type', ['course_invitation', 'event_invitation'])
            ->orderByDesc('created_at')
            ->get();

        if ($invitationNotifications->isEmpty()) {
            $this->info('No invitation records found.');
            return self::SUCCESS;
        }

        $expiredCandidates = collect();
        $lateUploadCandidates = collect();

        foreach ($invitationNotifications as $invitation) {
            $data = is_array($invitation->data) ? $invitation->data : [];
            $status = (string) data_get($data, 'invitation_status', 'pending');

            if ($status === 'pending') {
                $dueAtRaw = data_get($data, 'due_at');
                if (empty($dueAtRaw)) {
                    continue;
                }

                try {
                    $dueAt = Carbon::parse((string) $dueAtRaw);
                } catch (\Throwable $e) {
                    continue;
                }

                if (!$dueAt->lt($now)) {
                    continue;
                }

                $expiredCandidates->push([
                    'invitation' => $invitation,
                    'data' => $data,
                    'due_at' => $dueAt,
                ]);
            }

            if ($status === 'accepted') {
                $uploadDueAtRaw = (string) data_get($data, 'upload_due_at', '');
                if ($uploadDueAtRaw === '' || !empty(data_get($data, 'late_penalty_applied_at'))) {
                    continue;
                }

                try {
                    $uploadDueAt = Carbon::parse($uploadDueAtRaw);
                } catch (\Throwable $e) {
                    continue;
                }

                if (!$uploadDueAt->lt($now)) {
                    continue;
                }

                $lateUploadCandidates->push([
                    'invitation' => $invitation,
                    'data' => $data,
                    'upload_due_at' => $uploadDueAt,
                ]);
            }
        }

        if ($expiredCandidates->isEmpty() && $lateUploadCandidates->isEmpty()) {
            $this->info('No overdue pending invitations or late uploads.');
            return self::SUCCESS;
        }

        $invitationIds = $expiredCandidates
            ->pluck('invitation.id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $trainerExistingKeys = [];
        if (!empty($invitationIds)) {
            $trainerExistingKeys = TrainerNotification::query()
                ->where('type', 'invitation_overdue_alert')
                ->where(function ($query) use ($invitationIds) {
                    foreach ($invitationIds as $invitationId) {
                        $query->orWhere('data', 'like', '%"reminder_for_notification_id":' . $invitationId . '%');
                    }
                })
                ->get()
                ->map(function (TrainerNotification $notification) {
                    $data = is_array($notification->data) ? $notification->data : [];
                    return implode(':', [
                        (int) $notification->trainer_id,
                        (int) data_get($data, 'reminder_for_notification_id', 0),
                    ]);
                })
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $trainerExistingKeyMap = array_fill_keys($trainerExistingKeys, true);

        $adminUsers = User::query()
            ->where('role', 'admin')
            ->get(['id', 'name']);

        $adminExistingKeys = [];
        if (!empty($invitationIds)) {
            $adminExistingKeys = UserNotification::query()
                ->where('type', 'trainer_invitation_overdue_alert')
                ->where(function ($query) use ($invitationIds) {
                    foreach ($invitationIds as $invitationId) {
                        $query->orWhere('data', 'like', '%"reminder_for_notification_id":' . $invitationId . '%');
                    }
                })
                ->get()
                ->map(function (UserNotification $notification) {
                    $data = is_array($notification->data) ? $notification->data : [];
                    return implode(':', [
                        (int) $notification->user_id,
                        (int) data_get($data, 'reminder_for_notification_id', 0),
                    ]);
                })
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $adminExistingKeyMap = array_fill_keys($adminExistingKeys, true);

        $trainerAlertsSent = 0;
        $adminAlertsSent = 0;
        $activityService = app(TrainerActivityService::class);

        foreach ($expiredCandidates as $entry) {
            /** @var TrainerNotification $invitation */
            $invitation = $entry['invitation'];
            $data = $entry['data'];
            /** @var Carbon $dueAt */
            $dueAt = $entry['due_at'];

            $entityType = (string) data_get($data, 'entity_type', 'materi');
            $entityLabel = $entityType === 'event' ? 'event' : 'course';
            $entityId = (int) data_get($data, 'entity_id', 0);
            $dueText = $dueAt->translatedFormat('d M Y H:i');

            $invitation->invitation_status = 'expired';
            $invitation->responded_at = now();
            $data['invitation_status'] = 'expired';
            $data['expired_at'] = now()->toIso8601String();
            $invitation->data = $data;
            $invitation->save();

            $trainer = User::query()->find((int) $invitation->trainer_id);
            if ($trainer) {
                $activityService->recordExpiredInvitation($trainer);
            }

            $trainerKey = implode(':', [(int) $invitation->trainer_id, (int) $invitation->id]);
            if (!isset($trainerExistingKeyMap[$trainerKey])) {
                TrainerNotification::create([
                    'trainer_id' => (int) $invitation->trainer_id,
                    'type' => 'invitation_overdue_alert',
                    'title' => 'Undangan Expired',
                    'message' => 'SLA respon 24 jam untuk undangan ' . $entityLabel . ' telah habis (deadline: ' . $dueText . ').',
                    'data' => [
                        'entity_type' => $entityType,
                        'entity_id' => $entityId,
                        'url' => data_get($data, 'url'),
                        'due_at' => $dueAt->toIso8601String(),
                        'invitation_status' => 'expired',
                        'reminder_for_notification_id' => (int) $invitation->id,
                    ],
                    'expires_at' => now()->addDays(14),
                ]);

                $trainerExistingKeyMap[$trainerKey] = true;
                $trainerAlertsSent++;
            }

            $trainerName = (string) optional($invitation->trainer)->name;
            if ($trainerName === '') {
                $trainerName = 'Trainer #' . (int) $invitation->trainer_id;
            }

            foreach ($adminUsers as $admin) {
                $adminKey = implode(':', [(int) $admin->id, (int) $invitation->id]);
                if (isset($adminExistingKeyMap[$adminKey])) {
                    continue;
                }

                UserNotification::create([
                    'user_id' => (int) $admin->id,
                    'type' => 'trainer_invitation_overdue_alert',
                    'title' => 'Admin Action Needed: Undangan Expired',
                    'message' => 'Undangan trainer untuk ' . $trainerName . ' pada ' . $entityLabel . ' sudah expired (deadline: ' . $dueText . '). Cari trainer pengganti.',
                    'data' => [
                        'entity_type' => $entityType,
                        'entity_id' => $entityId,
                        'invitation_status' => 'expired',
                        'reminder_for_notification_id' => (int) $invitation->id,
                    ],
                    'expires_at' => now()->addDays(14),
                ]);

                $adminExistingKeyMap[$adminKey] = true;
                $adminAlertsSent++;
            }
        }

        $latePenaltiesApplied = 0;

        foreach ($lateUploadCandidates as $entry) {
            /** @var TrainerNotification $invitation */
            $invitation = $entry['invitation'];
            $data = $entry['data'];
            $entityType = (string) data_get($data, 'entity_type', 'course');
            $entityId = (int) data_get($data, 'entity_id', 0);

            if ($entityId <= 0 || $this->hasUploadedMaterial($entityType, $entityId)) {
                continue;
            }

            $trainer = User::query()->find((int) $invitation->trainer_id);
            if (!$trainer) {
                continue;
            }

            $entityTitle = '';
            if ($entityType === 'event') {
                $entityTitle = (string) (Event::query()->whereKey($entityId)->value('title') ?? '');
            }
            if ($entityType === 'course') {
                $entityTitle = (string) (Course::query()->whereKey($entityId)->value('name') ?? '');
            }

            $activityService->incrementLateUploads($trainer, [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'entity_title' => $entityTitle,
                'url' => data_get($data, 'url'),
            ]);

            $data['late_penalty_applied_at'] = now()->toIso8601String();
            $invitation->data = $data;
            $invitation->save();
            $latePenaltiesApplied++;
        }

        $this->info('Overdue trainer alerts sent: ' . $trainerAlertsSent);
        $this->info('Overdue admin alerts sent: ' . $adminAlertsSent);
        $this->info('Late upload penalties applied: ' . $latePenaltiesApplied);

        return self::SUCCESS;
    }

    private function hasUploadedMaterial(string $entityType, int $entityId): bool
    {
        if ($entityType === 'event') {
            $event = Event::query()->find($entityId);
            return $event ? !empty($event->module_path) : false;
        }

        if ($entityType === 'course') {
            $course = Course::query()->with('modules.quizQuestions')->find($entityId);
            if (!$course) {
                return false;
            }

            foreach ($course->modules as $module) {
                if (!empty($module->file_path) || !empty($module->content_url) || !empty($module->description)) {
                    return true;
                }

                if ((string) ($module->type ?? '') === 'quiz' && $module->quizQuestions->isNotEmpty()) {
                    return true;
                }
            }
        }

        return false;
    }
}
