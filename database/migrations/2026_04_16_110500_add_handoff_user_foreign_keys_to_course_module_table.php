<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('course_module', function (Blueprint $table) {
            if (Schema::hasColumn('course_module', 'assigned_by_admin_trainer_id')) {
                $table->foreign('assigned_by_admin_trainer_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }

            if (Schema::hasColumn('course_module', 'assigned_to_admin_course_id')) {
                $table->foreign('assigned_to_admin_course_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_module', function (Blueprint $table) {
            if (Schema::hasColumn('course_module', 'assigned_by_admin_trainer_id')) {
                $table->dropForeign(['assigned_by_admin_trainer_id']);
            }

            if (Schema::hasColumn('course_module', 'assigned_to_admin_course_id')) {
                $table->dropForeign(['assigned_to_admin_course_id']);
            }
        });
    }
};
