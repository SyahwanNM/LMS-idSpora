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
        if (!Schema::hasTable('teams')) {
            Schema::create('teams', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->string('name');
                $table->string('code')->unique();
                $table->unsignedBigInteger('leader_id');
                $table->string('status')->default('pending'); // pending, active, etc.
                $table->timestamps();

                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
                $table->foreign('leader_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Set index or add foreign key constraint on event_registrations to teams if not done
        try {
            Schema::table('event_registrations', function (Blueprint $table) {
                $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
            });
        } catch (\Throwable $e) {
            // Ignore if foreign key already exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('event_registrations', function (Blueprint $table) {
                $table->dropForeign(['team_id']);
            });
        } catch (\Throwable $e) {
            // Ignore if foreign key constraint does not exist
        }

        Schema::dropIfExists('teams');
    }
};
