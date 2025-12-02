<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'vbg_path')) {
                $table->string('vbg_path')->nullable()->after('image');
            }
            if (!Schema::hasColumn('events', 'certificate_path')) {
                $table->string('certificate_path')->nullable()->after('vbg_path');
            }
            if (!Schema::hasColumn('events', 'attendance_path')) {
                $table->string('attendance_path')->nullable()->after('certificate_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'vbg_path')) {
                $table->dropColumn('vbg_path');
            }
            if (Schema::hasColumn('events', 'certificate_path')) {
                $table->dropColumn('certificate_path');
            }
            if (Schema::hasColumn('events', 'attendance_path')) {
                $table->dropColumn('attendance_path');
            }
        });
    }
};
