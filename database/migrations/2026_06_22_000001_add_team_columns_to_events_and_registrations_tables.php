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
<<<<<<< HEAD
            $table->string('lomba_kategori')->default('individual')->after('jenis'); // individual, team, both
            $table->integer('max_team_members')->default(5)->after('lomba_kategori');
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->after('event_id');
            $table->boolean('is_team_leader')->default(false)->after('team_id');
=======
            if (!Schema::hasColumn('events', 'lomba_kategori')) {
                $table->string('lomba_kategori')->default('individual')->after('jenis'); // individual, team, both
            }
            if (!Schema::hasColumn('events', 'max_team_members')) {
                $table->integer('max_team_members')->default(5)->after('lomba_kategori');
            }
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('event_registrations', 'team_id')) {
                $table->unsignedBigInteger('team_id')->nullable()->after('event_id');
            }
            if (!Schema::hasColumn('event_registrations', 'is_team_leader')) {
                $table->boolean('is_team_leader')->default(false)->after('team_id');
            }
>>>>>>> b863fb54e2abec006fb54479f68889751e33734a
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
<<<<<<< HEAD
            $table->dropColumn(['lomba_kategori', 'max_team_members']);
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn(['team_id', 'is_team_leader']);
=======
            if (Schema::hasColumn('events', 'lomba_kategori')) {
                $table->dropColumn('lomba_kategori');
            }
            if (Schema::hasColumn('events', 'max_team_members')) {
                $table->dropColumn('max_team_members');
            }
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('event_registrations', 'team_id')) {
                $table->dropColumn('team_id');
            }
            if (Schema::hasColumn('event_registrations', 'is_team_leader')) {
                $table->dropColumn('is_team_leader');
            }
>>>>>>> b863fb54e2abec006fb54479f68889751e33734a
        });
    }
};
