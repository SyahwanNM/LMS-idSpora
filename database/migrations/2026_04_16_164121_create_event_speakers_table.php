<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_speakers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name'); // nama pembicara (bisa manual atau dari trainer)
            $table->decimal('salary', 15, 2)->default(0); // gaji pembicara
            $table->string('notes')->nullable(); // catatan tambahan
            $table->unsignedTinyInteger('order')->default(0); // urutan tampil
            $table->timestamps();

            $table->index(['event_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_speakers');
    }
};
