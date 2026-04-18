<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->unsignedInteger('unit_no');
            $table->string('title');
            $table->timestamps();

            $table->unique(['course_id', 'unit_no']);
            $table->index(['course_id', 'unit_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_units');
    }
};
