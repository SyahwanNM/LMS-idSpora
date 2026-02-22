<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'level')) {
                $table->string('level')->nullable()->after('title');
            }
            if (!Schema::hasColumn('events', 'event_time_end')) {
                $table->time('event_time_end')->nullable()->after('event_time');
            }
            if (!Schema::hasColumn('events', 'discount_until')) {
                $table->date('discount_until')->nullable()->after('discount_percentage');
            }
            if (!Schema::hasColumn('events', 'maps_url')) {
                $table->string('maps_url')->nullable()->after('location');
            }
            if (!Schema::hasColumn('events', 'zoom_link')) {
                $table->string('zoom_link')->nullable()->after('maps_url');
            }
            if (!Schema::hasColumn('events', 'benefit')) {
                $table->text('benefit')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'level')) {
                $table->dropColumn('level');
            }
            if (Schema::hasColumn('events', 'event_time_end')) {
                $table->dropColumn('event_time_end');
            }
            if (Schema::hasColumn('events', 'discount_until')) {
                $table->dropColumn('discount_until');
            }
            if (Schema::hasColumn('events', 'maps_url')) {
                $table->dropColumn('maps_url');
            }
            if (Schema::hasColumn('events', 'zoom_link')) {
                $table->dropColumn('zoom_link');
            }
            if (Schema::hasColumn('events', 'benefit')) {
                $table->dropColumn('benefit');
            }
        });
    }
};
