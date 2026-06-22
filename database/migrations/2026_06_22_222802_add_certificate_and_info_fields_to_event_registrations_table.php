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
            $table->string('full_name')->nullable()->after('user_id');
            $table->string('whatsapp_number')->nullable()->after('full_name');
            $table->string('institution_location')->nullable()->after('university_origin');
            $table->string('info_source')->nullable()->after('position');
            $table->string('educational_background')->nullable()->after('info_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'whatsapp_number',
                'institution_location',
                'info_source',
                'educational_background'
            ]);
        });
    }
};
