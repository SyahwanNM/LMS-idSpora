<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('courses') && !Schema::hasColumn('courses', 'trainer_contribution_scheme')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->string('trainer_contribution_scheme', 50)->nullable()->after('trainer_id');
                $table->unsignedTinyInteger('trainer_revenue_percent')->nullable()->after('trainer_contribution_scheme');
                $table->timestamp('trainer_scheme_accepted_at')->nullable()->after('trainer_revenue_percent');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (Schema::hasColumn('courses', 'trainer_scheme_accepted_at')) {
                    $table->dropColumn('trainer_scheme_accepted_at');
                }
                if (Schema::hasColumn('courses', 'trainer_revenue_percent')) {
                    $table->dropColumn('trainer_revenue_percent');
                }
                if (Schema::hasColumn('courses', 'trainer_contribution_scheme')) {
                    $table->dropColumn('trainer_contribution_scheme');
                }
            });
        }
    }
};