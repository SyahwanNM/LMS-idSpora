<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $this->realignEventsDeadlines();
        $this->realignExistingTrainerInvitations();
    }

    public function down(): void
    {
        // Non-reversible data migration.
    }

    private function realignEventsDeadlines(): void
    {
        if (!Schema::hasTable('events')) {
            return;
        }

        DB::table('events')
            ->select(['id', 'event_date', 'event_time'])
            ->whereNotNull('event_date')
            ->orderBy('id')
            ->chunkById(200, function ($events): void {
                foreach ($events as $event) {
                    $eventStartAt = $this->parseEventStartAt((string) $event->event_date, $event->event_time);
                    if ($eventStartAt === null) {
                        continue;
                    }

                    DB::table('events')
                        ->where('id', (int) $event->id)
                        ->update([
                            'material_deadline' => $eventStartAt->copy()->subDays(7),
                            'material_revision_deadline' => $eventStartAt->copy()->subDays(3),
                        ]);
                }
            });
    }

    private function realignExistingTrainerInvitations(): void
    {
        if (!Schema::hasTable('trainer_notifications')) {
            return;
        }

        $eventDeadlinesById = [];
        if (Schema::hasTable('events')) {
            DB::table('events')
                ->select(['id', 'material_deadline', 'material_revision_deadline'])
                ->orderBy('id')
                ->chunkById(500, function ($rows) use (&$eventDeadlinesById): void {
                    foreach ($rows as $row) {
                        $eventDeadlinesById[(int) $row->id] = [
                            'material_deadline' => $row->material_deadline,
                            'material_revision_deadline' => $row->material_revision_deadline,
                        ];
                    }
                });
        }

        DB::table('trainer_notifications')
            ->select(['id', 'type', 'data', 'created_at'])
            ->whereIn('type', ['course_invitation', 'event_invitation'])
            ->orderBy('id')
            ->chunkById(200, function ($notifications) use ($eventDeadlinesById): void {
                foreach ($notifications as $notification) {
                    $data = $this->decodePayload($notification->data);
                    if (empty($data)) {
                        continue;
                    }

                    if ((string) $notification->type === 'course_invitation') {
                        $createdAt = !empty($notification->created_at)
                            ? Carbon::parse((string) $notification->created_at)
                            : now();

                        $data['due_at'] = $createdAt->copy()->addDays(25)->toIso8601String();
                    }

                    if ((string) $notification->type === 'event_invitation') {
                        $eventId = (int) ($data['entity_id'] ?? 0);
                        $deadlines = $eventDeadlinesById[$eventId] ?? null;

                        if (!empty($deadlines)) {
                            $submission = !empty($deadlines['material_deadline'])
                                ? Carbon::parse((string) $deadlines['material_deadline'])->toIso8601String()
                                : null;
                            $revision = !empty($deadlines['material_revision_deadline'])
                                ? Carbon::parse((string) $deadlines['material_revision_deadline'])->toIso8601String()
                                : null;

                            $data['due_at'] = $submission;
                            $data['material_deadline'] = $submission;
                            $data['revision_due_at'] = $revision;
                            $data['material_revision_deadline'] = $revision;
                        }
                    }

                    DB::table('trainer_notifications')
                        ->where('id', (int) $notification->id)
                        ->update(['data' => json_encode($data)]);
                }
            });
    }

    private function parseEventStartAt(string $eventDate, $eventTime): ?Carbon
    {
        $date = trim($eventDate);
        if ($date === '') {
            return null;
        }

        $time = trim((string) ($eventTime ?? ''));
        if ($time === '') {
            $time = '00:00:00';
        }
        if (strlen($time) === 5) {
            $time .= ':00';
        }

        return Carbon::parse($date . ' ' . $time);
    }

    private function decodePayload($payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        if (is_object($payload)) {
            return (array) $payload;
        }

        if (!is_string($payload) || trim($payload) === '') {
            return [];
        }

        $decoded = json_decode($payload, true);

        return is_array($decoded) ? $decoded : [];
    }
};
