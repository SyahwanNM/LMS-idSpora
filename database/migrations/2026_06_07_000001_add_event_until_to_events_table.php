<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'event_until_date')) {
                $table->date('event_until_date')->nullable()->after('event_date')
                    ->comment('Custom end date; overrides event_date for isFinished logic');
            }
            if (!Schema::hasColumn('events', 'event_until_time')) {
                $table->time('event_until_time')->nullable()->after('event_until_date')
                    ->comment('Custom end time on event_until_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            foreach (['event_until_date', 'event_until_time'] as $col) {
                if (Schema::hasColumn('events', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
