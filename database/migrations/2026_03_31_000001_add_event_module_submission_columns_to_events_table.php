<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('events')) {
            return;
        }

        if (Schema::hasColumn('events', 'module_submission_path')) {
            return;
        }

        Schema::table('events', function (Blueprint $table) {
            // Trainer module submission (pending review by admin)
            if (Schema::hasColumn('events', 'module_path')) {
                $table->string('module_submission_path')->nullable()->after('module_path');
            } else {
                $table->string('module_submission_path')->nullable();
            }

            $table->timestamp('module_submitted_at')->nullable();
            $table->timestamp('module_verified_at')->nullable();
            $table->unsignedBigInteger('module_verified_by')->nullable();
            $table->timestamp('module_rejected_at')->nullable();
            $table->unsignedBigInteger('module_rejected_by')->nullable();
            $table->string('module_rejection_reason', 500)->nullable();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('events')) {
            return;
        }

        Schema::table('events', function (Blueprint $table) {
            $columns = [
                'module_submission_path',
                'module_submitted_at',
                'module_verified_at',
                'module_verified_by',
                'module_rejected_at',
                'module_rejected_by',
                'module_rejection_reason',
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('events', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
