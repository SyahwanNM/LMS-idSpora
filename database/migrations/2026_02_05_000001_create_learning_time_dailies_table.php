<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_time_dailies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->date('learned_on');
            $table->unsignedInteger('seconds')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'course_id', 'learned_on'], 'learning_time_dailies_user_course_day_unique');
            $table->index(['user_id', 'learned_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_time_dailies');
    }
};
