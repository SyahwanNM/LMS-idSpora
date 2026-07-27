<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('event_trainer_modules') && !Schema::hasColumn('event_trainer_modules', 'show_feedback')) {
            Schema::table('event_trainer_modules', function (Blueprint $table) {
                $table->boolean('show_feedback')->default(true)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('event_trainer_modules') && Schema::hasColumn('event_trainer_modules', 'show_feedback')) {
            Schema::table('event_trainer_modules', function (Blueprint $table) {
                $table->dropColumn('show_feedback');
            });
        }
    }
};
