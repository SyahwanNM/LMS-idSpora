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
        Schema::table('events', function (Blueprint $table) {
            $table->string('lomba_kategori')->default('individual')->after('jenis'); // individual, team, both
            $table->integer('max_team_members')->default(5)->after('lomba_kategori');
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->after('event_id');
            $table->boolean('is_team_leader')->default(false)->after('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['lomba_kategori', 'max_team_members']);
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn(['team_id', 'is_team_leader']);
        });
    }
};
