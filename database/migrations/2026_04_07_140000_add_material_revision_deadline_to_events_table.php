<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'material_revision_deadline')) {
            Schema::table('events', function (Blueprint $table) {
                $table->timestamp('material_revision_deadline')->nullable()->after('material_deadline');
            });
        }

        if (!Schema::hasTable('events')) {
            return;
        }

        DB::table('events')
            ->select(['id', 'event_date', 'event_time', 'material_deadline', 'material_revision_deadline'])
            ->orderBy('id')
            ->chunkById(200, function ($events): void {
                foreach ($events as $event) {
                    if (empty($event->event_date)) {
                        continue;
                    }

                    $time = trim((string) ($event->event_time ?? ''));
                    if ($time === '') {
                        $time = '00:00:00';
                    }
                    if (strlen($time) === 5) {
                        $time .= ':00';
                    }

                    $eventStartAt = Carbon::parse((string) $event->event_date . ' ' . $time);
                    $submissionDeadline = $eventStartAt->copy()->subDays(7);
                    $revisionDeadline = $eventStartAt->copy()->subDays(3);

                    $updates = [];
                    if (empty($event->material_deadline)) {
                        $updates['material_deadline'] = $submissionDeadline;
                    }
                    if (empty($event->material_revision_deadline)) {
                        $updates['material_revision_deadline'] = $revisionDeadline;
                    }

                    if (!empty($updates)) {
                        DB::table('events')->where('id', (int) $event->id)->update($updates);
                    }
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasTable('events') && Schema::hasColumn('events', 'material_revision_deadline')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('material_revision_deadline');
            });
        }
    }
};
