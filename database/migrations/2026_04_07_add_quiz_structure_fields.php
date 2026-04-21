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
        Schema::table('quizzez', function (Blueprint $table) {
            // Type of quiz: section_quiz or final_quiz
            if (!Schema::hasColumn('quizzez', 'quiz_type')) {
                $table->enum('quiz_type', ['section_quiz', 'final_quiz'])->default('section_quiz')->after('description');
            }

            // Which bagian/section is this quiz for (null if final_quiz)
            if (!Schema::hasColumn('quizzez', 'bagian_order_no')) {
                $table->integer('bagian_order_no')->nullable()->after('quiz_type');
            }

            // Time limit in minutes
            if (!Schema::hasColumn('quizzez', 'duration_minutes')) {
                $table->integer('duration_minutes')->default(10)->after('bagian_order_no');
            }

            // Number of questions (defaults: 5 for section, 20+ for final)
            if (!Schema::hasColumn('quizzez', 'num_questions')) {
                $table->integer('num_questions')->default(5)->after('duration_minutes');
            }

            // Is this quiz active/enabled
            if (!Schema::hasColumn('quizzez', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('num_questions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzez', function (Blueprint $table) {
            $columns = ['quiz_type', 'bagian_order_no', 'duration_minutes', 'num_questions', 'is_active'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('quizzez', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
