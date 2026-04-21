<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Backfill module-level review state for existing uploaded/text materials.
        DB::table('course_module')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('content_url')
                        ->where('content_url', '!=', '')
                        ->where('content_url', '!=', 'quiz_submitted');
                })->orWhere(function ($q) {
                    $q->where('type', 'pdf')
                        ->whereNotNull('description')
                        ->where('description', '!=', '');
                });
            })
            ->update([
                'review_status' => 'pending_review',
                'reviewed_at' => null,
                'reviewed_by' => null,
                'review_rejection_reason' => null,
                'updated_at' => now(),
            ]);

        // Backfill parent course status so old uploaded materials re-enter admin approval queue.
        DB::table('courses')
            ->whereNotNull('trainer_id')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('course_module')
                    ->whereColumn('course_module.course_id', 'courses.id')
                    ->where(function ($q) {
                        $q->where(function ($qq) {
                            $qq->whereNotNull('content_url')
                                ->where('content_url', '!=', '')
                                ->where('content_url', '!=', 'quiz_submitted');
                        })->orWhere(function ($qq) {
                            $qq->where('type', 'pdf')
                                ->whereNotNull('description')
                                ->where('description', '!=', '');
                        });
                    });
            })
            ->update([
                'status' => 'pending_review',
                'approved_at' => null,
                'approved_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // No automatic rollback for data backfill.
    }
};
