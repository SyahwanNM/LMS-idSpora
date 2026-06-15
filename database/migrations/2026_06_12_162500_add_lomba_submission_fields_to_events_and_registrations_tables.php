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
            $table->dateTime('start_submission')->nullable()->after('event_until_time');
            $table->dateTime('until_submission')->nullable()->after('start_submission');
            $table->dateTime('announcement_date')->nullable()->after('until_submission');
            $table->dateTime('until_submission_2')->nullable()->after('announcement_date');
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('submission_path')->nullable()->after('position');
            $table->timestamp('submission_uploaded_at')->nullable()->after('submission_path');
            $table->string('submission_status')->default('pending')->after('submission_uploaded_at'); // pending, lolos, tidak lolos
            $table->string('submission_path_2')->nullable()->after('submission_status');
            $table->timestamp('submission_2_uploaded_at')->nullable()->after('submission_path_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'start_submission',
                'until_submission',
                'announcement_date',
                'until_submission_2'
            ]);
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'submission_path',
                'submission_uploaded_at',
                'submission_status',
                'submission_path_2',
                'submission_2_uploaded_at'
            ]);
        });
    }
};
