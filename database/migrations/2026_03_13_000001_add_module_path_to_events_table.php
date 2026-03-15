<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('events') || Schema::hasColumn('events', 'module_path')) {
            return;
        }

        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'attendance_path')) {
                $table->string('module_path')->nullable()->after('attendance_path');
                return;
            }
            if (Schema::hasColumn('events', 'certificate_path')) {
                $table->string('module_path')->nullable()->after('certificate_path');
                return;
            }
            $table->string('module_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'module_path')) {
                $table->dropColumn('module_path');
            }
        });
    }
};
