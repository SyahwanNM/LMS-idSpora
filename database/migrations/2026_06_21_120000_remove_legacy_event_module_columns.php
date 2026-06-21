<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            // Backfill data from events to event_trainer_modules
            $events = DB::table('events')
                ->whereNotNull('module_path')
                ->where('module_path', '<>', '')
                ->get();

            foreach ($events as $event) {
                $trainerId = $event->trainer_id;
                if (!$trainerId) {
                    $trainerId = DB::table('users')->where('role', 'trainer')->value('id');
                }

                if ($trainerId) {
                    $exists = DB::table('event_trainer_modules')
                        ->where('event_id', $event->id)
                        ->where('trainer_id', $trainerId)
                        ->exists();

                    if (!$exists) {
                        $status = 'pending_review';
                        if (isset($event->material_status)) {
                            if ($event->material_status === 'approved') {
                                $status = 'approved';
                            } elseif ($event->material_status === 'rejected') {
                                $status = 'rejected';
                            }
                        }

                        $originalName = basename((string) $event->module_path);
                        if (empty($originalName)) {
                            $originalName = 'Module.pdf';
                        }

                        DB::table('event_trainer_modules')->insert([
                            'event_id' => $event->id,
                            'trainer_id' => $trainerId,
                            'original_name' => $originalName,
                            'path' => $event->module_path,
                            'status' => $status,
                            'rejection_reason' => $event->material_rejection_reason ?? $event->module_rejection_reason ?? null,
                            'reviewed_by' => $event->material_approved_by ?? $event->module_verified_by ?? $event->module_rejected_by ?? null,
                            'reviewed_at' => $event->material_approved_at ?? $event->module_verified_at ?? $event->module_rejected_at ?? null,
                            'created_at' => $event->module_submitted_at ?? $event->created_at ?? now(),
                            'updated_at' => $event->updated_at ?? now(),
                        ]);
                    }
                }
            }

            // Drop foreign keys first (try-catch because it might not exist in some environments)
            try {
                Schema::table('events', function (Blueprint $table) {
                    if (Schema::hasColumn('events', 'material_approved_by')) {
                        $table->dropForeign(['material_approved_by']);
                    }
                });
            } catch (\Throwable $e) {
                // Ignore if constraint does not exist
            }

            // Drop columns
            Schema::table('events', function (Blueprint $table) {
                $columns = [
                    'module_path',
                    'material_status',
                    'module_submission_path',
                    'module_submitted_at',
                    'module_verified_at',
                    'module_verified_by',
                    'material_approved_at',
                    'material_approved_by',
                    'material_rejection_reason',
                    'module_rejected_at',
                    'module_rejected_by',
                    'module_rejection_reason',
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('events', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                $table->string('module_path')->nullable();
                $table->string('module_submission_path')->nullable();
                $table->enum('material_status', ['pending', 'pending_review', 'approved', 'rejected'])->default('pending');
                $table->timestamp('material_approved_at')->nullable();
                $table->unsignedBigInteger('material_approved_by')->nullable();
                $table->text('material_rejection_reason')->nullable();
                $table->timestamp('module_submitted_at')->nullable();
                $table->timestamp('module_verified_at')->nullable();
                $table->unsignedBigInteger('module_verified_by')->nullable();
                $table->timestamp('module_rejected_at')->nullable();
                $table->unsignedBigInteger('module_rejected_by')->nullable();
                $table->string('module_rejection_reason', 500)->nullable();
            });
        }
    }
};
