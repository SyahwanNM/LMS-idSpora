<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'show_feedback')) {
            Schema::table('events', function (Blueprint $table) {
                $table->boolean('show_feedback')->default(true)->after('is_published');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events') && Schema::hasColumn('events', 'show_feedback')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('show_feedback');
            });
        }
    }
};
