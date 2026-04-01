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
        Schema::create('course_template_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_template_id')->constrained('course_templates')->cascadeOnDelete();
            $table->unsignedInteger('order_no');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['video', 'pdf', 'quiz'])->default('video');
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('duration')->default(0);
            $table->timestamps();

            $table->unique(['course_template_id', 'order_no']);
            $table->index(['course_template_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_template_modules');
    }
};
