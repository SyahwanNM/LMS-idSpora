<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('manual_payments')) {
            return;
        }

        Schema::table('manual_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('manual_payments', 'course_id')) {
                $table->foreignId('course_id')->nullable()->after('event_registration_id')->constrained('courses')->onDelete('cascade');
            }
            if (!Schema::hasColumn('manual_payments', 'enrollment_id')) {
                $table->foreignId('enrollment_id')->nullable()->after('course_id')->constrained('enrollments')->onDelete('set null');
            }
            if (!Schema::hasColumn('manual_payments', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable()->after('method');
            }
            if (!Schema::hasColumn('manual_payments', 'referral_code')) {
                $table->string('referral_code')->nullable()->after('whatsapp_number');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('manual_payments')) {
            return;
        }

        Schema::table('manual_payments', function (Blueprint $table) {
            if (Schema::hasColumn('manual_payments', 'enrollment_id')) {
                $table->dropConstrainedForeignId('enrollment_id');
            }
            if (Schema::hasColumn('manual_payments', 'course_id')) {
                $table->dropConstrainedForeignId('course_id');
            }
            if (Schema::hasColumn('manual_payments', 'referral_code')) {
                $table->dropColumn('referral_code');
            }
            if (Schema::hasColumn('manual_payments', 'whatsapp_number')) {
                $table->dropColumn('whatsapp_number');
            }
        });
    }
};
