<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('payments')) {
            return; // nothing to do
        }
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'event_id')) {
                $table->unsignedBigInteger('event_id')->nullable()->after('user_id');
                $table->index('event_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('payments')) { return; }
        Schema::table('payments', function (Blueprint $table) {
            // We won't drop columns on down to avoid accidental data loss
        });
    }
};
