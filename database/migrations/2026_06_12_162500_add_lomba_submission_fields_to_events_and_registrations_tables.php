<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'start_submission')) {
                $table->dateTime('start_submission')->nullable()->after('event_until_time');
            }
            if (!Schema::hasColumn('events', 'until_submission')) {
                $table->dateTime('until_submission')->nullable()->after('start_submission');
            }
            if (!Schema::hasColumn('events', 'announcement_date')) {
                $table->dateTime('announcement_date')->nullable()->after('until_submission');
            }
            if (!Schema::hasColumn('events', 'until_submission_2')) {
                $table->dateTime('until_submission_2')->nullable()->after('announcement_date');
            }
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('event_registrations', 'submission_path')) {
                $table->string('submission_path')->nullable()->after('position');
            }
            if (!Schema::hasColumn('event_registrations', 'submission_uploaded_at')) {
                $table->timestamp('submission_uploaded_at')->nullable()->after('submission_path');
            }
            if (!Schema::hasColumn('event_registrations', 'submission_status')) {
                $table->string('submission_status')->default('pending')->after('submission_uploaded_at');
            }
            if (!Schema::hasColumn('event_registrations', 'submission_path_2')) {
                $table->string('submission_path_2')->nullable()->after('submission_status');
            }
            if (!Schema::hasColumn('event_registrations', 'submission_2_uploaded_at')) {
                $table->timestamp('submission_2_uploaded_at')->nullable()->after('submission_path_2');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $cols = [];
            foreach (['start_submission', 'until_submission', 'announcement_date', 'until_submission_2'] as $c) {
                if (Schema::hasColumn('events', $c)) {
                    $cols[] = $c;
                }
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $cols = [];
            foreach (['submission_path', 'submission_uploaded_at', 'submission_status', 'submission_path_2', 'submission_2_uploaded_at'] as $c) {
                if (Schema::hasColumn('event_registrations', $c)) {
                    $cols[] = $c;
                }
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
