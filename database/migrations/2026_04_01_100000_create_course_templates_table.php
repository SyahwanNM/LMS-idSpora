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
        Schema::create('course_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->unsignedInteger('version')->default(1);
            $table->enum('status', ['active', 'archive'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['status', 'level']);
            $table->unique(['name', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_templates');
    }
};
