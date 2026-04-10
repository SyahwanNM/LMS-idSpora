<?php

namespace App\Console\Commands;

use App\Models\TrainerNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendTrainerInvitationDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainer:send-invitation-deadline-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automatic H-2 and H-1 reminders for pending trainer invitations';

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
            $this->info('No invitations found.');
            return self::SUCCESS;
        }

        $candidateInvitations = collect();

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

            $daysLeft = $now->copy()->startOfDay()->diffInDays($dueAt->copy()->startOfDay(), false);
            if (!in_array($daysLeft, [1, 2], true)) {
                continue;
            }

            $candidateInvitations->push([
                'invitation' => $invitation,
                'data' => $data,
                'due_at' => $dueAt,
                'days_left' => $daysLeft,
            ]);
        }

        if ($candidateInvitations->isEmpty()) {
            $this->info('No H-2/H-1 reminders to send.');
            return self::SUCCESS;
        }

        $invitationIds = $candidateInvitations
            ->pluck('invitation.id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $existingReminderKeys = TrainerNotification::query()
            ->where('type', 'invitation_deadline_reminder')
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
                    (int) data_get($data, 'reminder_day', 0),
                ]);
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        $existingReminderKeys = array_fill_keys($existingReminderKeys, true);

        $sent = 0;

        foreach ($candidateInvitations as $candidate) {
            /** @var TrainerNotification $invitation */
            $invitation = $candidate['invitation'];
            $data = $candidate['data'];
            /** @var Carbon $dueAt */
            $dueAt = $candidate['due_at'];
            $daysLeft = (int) $candidate['days_left'];

            $key = implode(':', [(int) $invitation->trainer_id, (int) $invitation->id, $daysLeft]);
            if (isset($existingReminderKeys[$key])) {
                continue;
            }

            $entityType = (string) data_get($data, 'entity_type', 'materi');
            $entityLabel = $entityType === 'event' ? 'event' : 'course';
            $deadlineText = $dueAt->translatedFormat('d M Y H:i');

            TrainerNotification::create([
                'trainer_id' => (int) $invitation->trainer_id,
                'type' => 'invitation_deadline_reminder',
                'title' => 'Reminder Deadline Pengumpulan Materi (H-' . $daysLeft . ')',
                'message' => 'Batas waktu konfirmasi dan pengumpulan materi untuk ' . $entityLabel . ' tersisa ' . $daysLeft . ' hari (deadline: ' . $deadlineText . ').',
                'data' => [
                    'entity_type' => data_get($data, 'entity_type'),
                    'entity_id' => data_get($data, 'entity_id'),
                    'url' => data_get($data, 'url'),
                    'due_at' => $dueAt->toIso8601String(),
                    'reminder_for_notification_id' => (int) $invitation->id,
                    'reminder_day' => $daysLeft,
                ],
                'expires_at' => $dueAt,
            ]);

            $sent++;
            $existingReminderKeys[$key] = true;
        }

        $this->info('Invitation reminders sent: ' . $sent);

        return self::SUCCESS;
    }
}
