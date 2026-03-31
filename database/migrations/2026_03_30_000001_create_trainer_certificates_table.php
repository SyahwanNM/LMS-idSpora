<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('certifiable'); // Event / Course
            $table->string('activity_code', 3);
            $table->string('type_code', 3);
            $table->string('sequence', 10)->default('001');
            $table->string('certificate_number')->unique();
            $table->timestamp('issued_at')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('sent'); // sent / revoked
            $table->timestamps();

            $table->index(['trainer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_certificates');
    }
};

