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
<<<<<<< HEAD
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

        // Set index or add foreign key constraint on event_registrations to teams if not done
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
        });
=======
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
>>>>>>> b863fb54e2abec006fb54479f68889751e33734a
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<< HEAD
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
        });
=======
        try {
            Schema::table('event_registrations', function (Blueprint $table) {
                $table->dropForeign(['team_id']);
            });
        } catch (\Throwable $e) {
            // Ignore if foreign key constraint does not exist
        }
>>>>>>> b863fb54e2abec006fb54479f68889751e33734a

        Schema::dropIfExists('teams');
    }
};
