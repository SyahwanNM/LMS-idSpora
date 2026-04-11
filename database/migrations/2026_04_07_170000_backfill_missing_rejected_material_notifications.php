<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('trainer_notifications')) {
            return;
        }

        $existingKeys = $this->buildExistingRejectedNotificationKeys();

        $this->backfillRejectedEventNotifications($existingKeys);
        $this->backfillRejectedCourseNotifications($existingKeys);
    }

    public function down(): void
    {
        // Non-reversible data migration.
    }

    private function buildExistingRejectedNotificationKeys(): array
    {
        $keys = [];

        DB::table('trainer_notifications')
            ->select(['trainer_id', 'type', 'data'])
            ->whereIn('type', ['event_material_rejected', 'course_material_rejected'])
            ->orderBy('id')
            ->chunk(500, function ($rows) use (&$keys): void {
                foreach ($rows as $row) {
                    $trainerId = (int) ($row->trainer_id ?? 0);
                    if ($trainerId <= 0) {
                        continue;
                    }

                    $payload = $this->decodePayload($row->data);
                    $entityType = (string) ($payload['entity_type'] ?? '');
                    $entityId = (int) ($payload['entity_id'] ?? 0);

                    if ($entityType === '' || $entityId <= 0) {
                        continue;
                    }

                    $keys[$trainerId . ':' . $entityType . ':' . $entityId] = true;
                }
            });

        return $keys;
    }

    private function backfillRejectedEventNotifications(array &$existingKeys): void
    {
        if (!Schema::hasTable('events')) {
            return;
        }

        DB::table('events')
            ->select(['id', 'trainer_id', 'title', 'material_rejection_reason'])
            ->where('material_status', 'rejected')
            ->whereNotNull('trainer_id')
            ->orderBy('id')
            ->chunkById(200, function ($events) use (&$existingKeys): void {
                foreach ($events as $event) {
                    $trainerId = (int) ($event->trainer_id ?? 0);
                    $eventId = (int) ($event->id ?? 0);
                    if ($trainerId <= 0 || $eventId <= 0) {
                        continue;
                    }

                    $key = $trainerId . ':event:' . $eventId;
                    if (isset($existingKeys[$key])) {
                        continue;
                    }

                    $reason = trim((string) ($event->material_rejection_reason ?? ''));
                    if ($reason === '') {
                        $reason = 'Materi perlu disesuaikan dengan standar review admin.';
                    }

                    DB::table('trainer_notifications')->insert([
                        'trainer_id' => $trainerId,
                        'type' => 'event_material_rejected',
                        'title' => 'Materi Event Perlu Revisi',
                        'message' => 'Materi event "' . (string) ($event->title ?? 'Event') . '" ditolak. Catatan admin: ' . $reason,
                        'data' => json_encode([
                            'entity_type' => 'event',
                            'entity_id' => $eventId,
                            'rejection_reason' => $reason,
                            'url' => $this->safeRoute('trainer.events.studio', $eventId),
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                        'expires_at' => now()->addDays(30),
                    ]);

                    $existingKeys[$key] = true;
                }
            });
    }

    private function backfillRejectedCourseNotifications(array &$existingKeys): void
    {
        if (!Schema::hasTable('courses')) {
            return;
        }

        DB::table('courses')
            ->select(['id', 'trainer_id', 'name', 'rejection_reason'])
            ->where('status', 'rejected')
            ->whereNotNull('trainer_id')
            ->orderBy('id')
            ->chunkById(200, function ($courses) use (&$existingKeys): void {
                foreach ($courses as $course) {
                    $trainerId = (int) ($course->trainer_id ?? 0);
                    $courseId = (int) ($course->id ?? 0);
                    if ($trainerId <= 0 || $courseId <= 0) {
                        continue;
                    }

                    $key = $trainerId . ':course:' . $courseId;
                    if (isset($existingKeys[$key])) {
                        continue;
                    }

                    $reason = trim((string) ($course->rejection_reason ?? ''));
                    if ($reason === '') {
                        $reason = 'Materi perlu disesuaikan dengan standar review admin.';
                    }

                    DB::table('trainer_notifications')->insert([
                        'trainer_id' => $trainerId,
                        'type' => 'course_material_rejected',
                        'title' => 'Materi Course Perlu Revisi',
                        'message' => 'Materi course "' . (string) ($course->name ?? 'Course') . '" ditolak. Catatan admin: ' . $reason,
                        'data' => json_encode([
                            'entity_type' => 'course',
                            'entity_id' => $courseId,
                            'rejection_reason' => $reason,
                            'url' => $this->safeRoute('trainer.courses.studio', $courseId),
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                        'expires_at' => now()->addDays(30),
                    ]);

                    $existingKeys[$key] = true;
                }
            });
    }

    private function safeRoute(string $name, int $id): ?string
    {
        try {
            return route($name, $id);
        } catch (\Throwable $e) {
            return null;
        }
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
