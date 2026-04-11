<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'total_courses_completed')) {
                $table->unsignedInteger('total_courses_completed')->default(0)->after('last_event_date');
            }

            if (!Schema::hasColumn('users', 'average_rating')) {
                $table->decimal('average_rating', 4, 2)->default(0)->after('total_courses_completed');
            }

            if (!Schema::hasColumn('users', 'late_uploads')) {
                $table->unsignedInteger('late_uploads')->default(0)->after('average_rating');
            }

            if (!Schema::hasColumn('users', 'trainer_tier')) {
                $table->string('trainer_tier', 20)->default('associate')->after('late_uploads');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'trainer_tier')) {
                $table->dropColumn('trainer_tier');
            }

            if (Schema::hasColumn('users', 'late_uploads')) {
                $table->dropColumn('late_uploads');
            }

            if (Schema::hasColumn('users', 'average_rating')) {
                $table->dropColumn('average_rating');
            }

            if (Schema::hasColumn('users', 'total_courses_completed')) {
                $table->dropColumn('total_courses_completed');
            }
        });
    }
};