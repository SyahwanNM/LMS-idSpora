<?php

namespace App\Console\Commands;

use App\Models\TrainerNotification;
use App\Models\User;
use App\Models\UserNotification;
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

        $pendingInvitations = TrainerNotification::query()
            ->whereIn('type', ['course_invitation', 'event_invitation'])
            ->orderByDesc('created_at')
            ->get();

        if ($pendingInvitations->isEmpty()) {
            $this->info('No invitation records found.');
            return self::SUCCESS;
        }

        $overdueInvitations = collect();

        foreach ($pendingInvitations as $invitation) {
            $data = is_array($invitation->data) ? $invitation->data : [];
            $status = (string) data_get($data, 'invitation_status', 'pending');
            if ($status !== 'pending') {
                continue;
            }

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

            $overdueInvitations->push([
                'invitation' => $invitation,
                'data' => $data,
                'due_at' => $dueAt,
            ]);
        }

        if ($overdueInvitations->isEmpty()) {
            $this->info('No overdue pending invitations.');
            return self::SUCCESS;
        }

        $invitationIds = $overdueInvitations
            ->pluck('invitation.id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

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

        $trainerExistingKeyMap = array_fill_keys($trainerExistingKeys, true);

        $adminUsers = User::query()
            ->where('role', 'admin')
            ->get(['id', 'name']);

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

        $adminExistingKeyMap = array_fill_keys($adminExistingKeys, true);

        $trainerAlertsSent = 0;
        $adminAlertsSent = 0;

        foreach ($overdueInvitations as $entry) {
            /** @var TrainerNotification $invitation */
            $invitation = $entry['invitation'];
            $data = $entry['data'];
            /** @var Carbon $dueAt */
            $dueAt = $entry['due_at'];

            $entityType = (string) data_get($data, 'entity_type', 'materi');
            $entityLabel = $entityType === 'event' ? 'event' : 'course';
            $entityId = (int) data_get($data, 'entity_id', 0);
            $dueText = $dueAt->translatedFormat('d M Y H:i');

            $trainerKey = implode(':', [(int) $invitation->trainer_id, (int) $invitation->id]);
            if (!isset($trainerExistingKeyMap[$trainerKey])) {
                TrainerNotification::create([
                    'trainer_id' => (int) $invitation->trainer_id,
                    'type' => 'invitation_overdue_alert',
                    'title' => 'Eskalasi: Deadline Terlewat',
                    'message' => 'Deadline konfirmasi/pengumpulan materi untuk ' . $entityLabel . ' telah terlewat (deadline: ' . $dueText . '). Segera tindaklanjuti.',
                    'data' => [
                        'entity_type' => $entityType,
                        'entity_id' => $entityId,
                        'url' => data_get($data, 'url'),
                        'due_at' => $dueAt->toIso8601String(),
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
                    'title' => 'Eskalasi Admin: Deadline Trainer Terlewat',
                    'message' => $trainerName . ' melewati deadline untuk ' . $entityLabel . ' (deadline: ' . $dueText . ').',
                    'data' => [
                        'entity_type' => $entityType,
                        'entity_id' => $entityId,
                        'reminder_for_notification_id' => (int) $invitation->id,
                    ],
                    'expires_at' => now()->addDays(14),
                ]);

                $adminExistingKeyMap[$adminKey] = true;
                $adminAlertsSent++;
            }
        }

        $this->info('Overdue trainer alerts sent: ' . $trainerAlertsSent);
        $this->info('Overdue admin alerts sent: ' . $adminAlertsSent);

        return self::SUCCESS;
    }
}
