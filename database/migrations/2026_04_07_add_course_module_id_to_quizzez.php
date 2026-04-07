<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quizzez', function (Blueprint $table) {
            // Link quiz to a specific module (for section quizzes)
            if (!Schema::hasColumn('quizzez', 'course_module_id')) {
                $table->foreignId('course_module_id')->nullable()->after('course_id')->constrained('course_module')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzez', function (Blueprint $table) {
            if (Schema::hasColumn('quizzez', 'course_module_id')) {
                $table->dropForeignIdFor('CourseModule');
                $table->dropColumn('course_module_id');
            }
        });
    }
};
