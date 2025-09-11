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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->date('issued_date');
            $table->date('expiry_date')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->string(column: 'certificate_code')->unique();
            $table->string('file_path')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('certificate_templates')->onDelete('cascade');

            // Polymorphic relation to issuable entities (Course, Event, etc.)
            $table->unsignedBigInteger('certifiable_id');
            $table->string('certifiable_type');
            $table->index(['certifiable_id', 'certifiable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('certificates');
    }
};
