<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainer_payments', function (Blueprint $table) {
            // Type: 'course_payout' | 'event_fee' | 'manual'
            $table->string('type')->default('manual')->after('user_id');
            $table->unsignedBigInteger('event_id')->nullable()->after('type');
            $table->unsignedBigInteger('course_id')->nullable()->after('event_id');
            $table->string('trainer_name')->nullable()->after('notes');

            $table->foreign('event_id')->references('id')->on('events')->onDelete('set null');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('trainer_payments', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropForeign(['course_id']);
            $table->dropColumn(['type', 'event_id', 'course_id', 'trainer_name']);
        });
    }
};
