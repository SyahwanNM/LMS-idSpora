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
        Schema::table('event_registrations', function (Blueprint $table) {
<<<<<<< HEAD
            $table->string('team_name')->nullable()->after('whatsapp_number');
=======
            if (!Schema::hasColumn('event_registrations', 'team_name')) {
                $table->string('team_name')->nullable()->after('whatsapp_number');
            }
>>>>>>> b863fb54e2abec006fb54479f68889751e33734a
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
<<<<<<< HEAD
            $table->dropColumn('team_name');
=======
            if (Schema::hasColumn('event_registrations', 'team_name')) {
                $table->dropColumn('team_name');
            }
>>>>>>> b863fb54e2abec006fb54479f68889751e33734a
        });
    }
};
