<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('event_registrations')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                if (!Schema::hasColumn('event_registrations', 'attendance_status')) {
                    $table->string('attendance_status')->nullable()->after('status');
                }
                if (!Schema::hasColumn('event_registrations', 'attended_at')) {
                    $table->timestamp('attended_at')->nullable()->after('attendance_status');
                }
                if (!Schema::hasColumn('event_registrations', 'attendance_code')) {
                    $table->string('attendance_code', 32)->nullable()->index()->after('attended_at');
                }
                if (!Schema::hasColumn('event_registrations', 'attendance_code_used_at')) {
                    $table->timestamp('attendance_code_used_at')->nullable()->after('attendance_code');
                }
            });
        }
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (!Schema::hasColumn('events', 'attendance_qr_token')) {
                    $table->string('attendance_qr_token', 64)->nullable()->after('zoom_link');
                }
                if (!Schema::hasColumn('events', 'attendance_qr_image')) {
                    $table->string('attendance_qr_image')->nullable()->after('attendance_qr_token');
                }
                if (!Schema::hasColumn('events', 'attendance_qr_generated_at')) {
                    $table->timestamp('attendance_qr_generated_at')->nullable()->after('attendance_qr_image');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('event_registrations')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                if (Schema::hasColumn('event_registrations', 'attendance_code_used_at')) {
                    $table->dropColumn('attendance_code_used_at');
                }
                if (Schema::hasColumn('event_registrations', 'attendance_code')) {
                    $table->dropColumn('attendance_code');
                }
                if (Schema::hasColumn('event_registrations', 'attended_at')) {
                    $table->dropColumn('attended_at');
                }
                if (Schema::hasColumn('event_registrations', 'attendance_status')) {
                    $table->dropColumn('attendance_status');
                }
            });
        }
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (Schema::hasColumn('events', 'attendance_qr_generated_at')) {
                    $table->dropColumn('attendance_qr_generated_at');
                }
                if (Schema::hasColumn('events', 'attendance_qr_image')) {
                    $table->dropColumn('attendance_qr_image');
                }
                if (Schema::hasColumn('events', 'attendance_qr_token')) {
                    $table->dropColumn('attendance_qr_token');
                }
            });
        }
    }
};
