<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('trainer_assignments')) {
            return;
        }

        Schema::table('trainer_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('trainer_assignments', 'material_path')) {
                $table->string('material_path')->nullable()->after('materials_uploaded_at');
            }
            if (!Schema::hasColumn('trainer_assignments', 'material_status')) {
                $table->enum('material_status', ['pending', 'pending_review', 'approved', 'rejected'])->default('pending')->after('material_path');
            }
            if (!Schema::hasColumn('trainer_assignments', 'material_submitted_at')) {
                $table->timestamp('material_submitted_at')->nullable()->after('material_status');
            }
            if (!Schema::hasColumn('trainer_assignments', 'material_approved_at')) {
                $table->timestamp('material_approved_at')->nullable()->after('material_submitted_at');
            }
            if (!Schema::hasColumn('trainer_assignments', 'material_approved_by')) {
                $table->foreignId('material_approved_by')->nullable()->constrained('users')->nullOnDelete()->after('material_approved_at');
            }
            if (!Schema::hasColumn('trainer_assignments', 'material_rejected_at')) {
                $table->timestamp('material_rejected_at')->nullable()->after('material_approved_by');
            }
            if (!Schema::hasColumn('trainer_assignments', 'material_rejected_by')) {
                $table->foreignId('material_rejected_by')->nullable()->constrained('users')->nullOnDelete()->after('material_rejected_at');
            }
            if (!Schema::hasColumn('trainer_assignments', 'material_rejection_reason')) {
                $table->text('material_rejection_reason')->nullable()->after('material_rejected_by');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('trainer_assignments')) {
            return;
        }

        Schema::table('trainer_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('trainer_assignments', 'material_approved_by')) {
                $table->dropForeign(['material_approved_by']);
            }
            if (Schema::hasColumn('trainer_assignments', 'material_rejected_by')) {
                $table->dropForeign(['material_rejected_by']);
            }

            $dropColumns = array_values(array_filter([
                Schema::hasColumn('trainer_assignments', 'material_path') ? 'material_path' : null,
                Schema::hasColumn('trainer_assignments', 'material_status') ? 'material_status' : null,
                Schema::hasColumn('trainer_assignments', 'material_submitted_at') ? 'material_submitted_at' : null,
                Schema::hasColumn('trainer_assignments', 'material_approved_at') ? 'material_approved_at' : null,
                Schema::hasColumn('trainer_assignments', 'material_approved_by') ? 'material_approved_by' : null,
                Schema::hasColumn('trainer_assignments', 'material_rejected_at') ? 'material_rejected_at' : null,
                Schema::hasColumn('trainer_assignments', 'material_rejected_by') ? 'material_rejected_by' : null,
                Schema::hasColumn('trainer_assignments', 'material_rejection_reason') ? 'material_rejection_reason' : null,
            ]));

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
