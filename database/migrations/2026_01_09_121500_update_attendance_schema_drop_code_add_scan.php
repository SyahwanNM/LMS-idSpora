<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('event_registrations')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                if (Schema::hasColumn('event_registrations', 'attendance_code_used_at')) {
                    $table->dropColumn('attendance_code_used_at');
                }
                if (Schema::hasColumn('event_registrations', 'attendance_code')) {
                    $table->dropColumn('attendance_code');
                }
                if (!Schema::hasColumn('event_registrations', 'attendance_scan_qr')) {
                    $table->timestamp('attendance_scan_qr')->nullable()->after('attended_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('event_registrations')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                if (Schema::hasColumn('event_registrations', 'attendance_scan_qr')) {
                    $table->dropColumn('attendance_scan_qr');
                }
                if (!Schema::hasColumn('event_registrations', 'attendance_code')) {
                    $table->string('attendance_code', 32)->nullable()->index();
                }
                if (!Schema::hasColumn('event_registrations', 'attendance_code_used_at')) {
                    $table->timestamp('attendance_code_used_at')->nullable();
                }
            });
        }
    }
};
