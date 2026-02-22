<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'points')) {
                $table->unsignedInteger('points')->default(0)->after('bio');
            }
            if (!Schema::hasColumn('users', 'badge')) {
                $table->string('badge', 50)->default('beginner')->after('points');
            }
            if (!Schema::hasColumn('users', 'last_event_date')) {
                $table->date('last_event_date')->nullable()->after('badge');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'points')) {
                $table->dropColumn('points');
            }
            if (Schema::hasColumn('users', 'badge')) {
                $table->dropColumn('badge');
            }
            if (Schema::hasColumn('users', 'last_event_date')) {
                $table->dropColumn('last_event_date');
            }
        });
    }
};
