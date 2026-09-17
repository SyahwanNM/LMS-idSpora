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
        if (!Schema::hasTable('event_winner_categories')) {
            Schema::create('event_winner_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
                $table->string('name');
                $table->string('certificate_template')->default('template_1');
                $table->json('certificate_custom_template')->nullable();
                $table->longText('certificate_logo')->nullable();
                $table->longText('certificate_signature')->nullable();
                $table->string('file_tambahan')->nullable();
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('event_registration_winners')) {
            Schema::create('event_registration_winners', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
                $table->foreignId('event_registration_id')->constrained('event_registrations')->cascadeOnDelete();
                $table->foreignId('event_winner_category_id')->constrained('event_winner_categories')->cascadeOnDelete();
                $table->string('winner_title')->nullable();
                $table->timestamps();

                $table->index(['event_id', 'event_registration_id']);
                $table->index('event_winner_category_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registration_winners');
        Schema::dropIfExists('event_winner_categories');
    }
};
