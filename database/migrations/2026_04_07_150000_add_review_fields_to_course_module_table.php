<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_module', function (Blueprint $table) {
            if (!Schema::hasColumn('course_module', 'review_status')) {
                $table->string('review_status', 20)->nullable()->after('duration');
            }

            if (!Schema::hasColumn('course_module', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('review_status');
            }

            if (!Schema::hasColumn('course_module', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('course_module', 'review_rejection_reason')) {
                $table->text('review_rejection_reason')->nullable()->after('reviewed_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_module', function (Blueprint $table) {
            if (Schema::hasColumn('course_module', 'reviewed_by')) {
                $table->dropConstrainedForeignId('reviewed_by');
            }

            $toDrop = [];
            foreach (['review_status', 'reviewed_at', 'review_rejection_reason'] as $column) {
                if (Schema::hasColumn('course_module', $column)) {
                    $toDrop[] = $column;
                }
            }

            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }
};
