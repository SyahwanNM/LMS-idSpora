<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Per-day QR codes for multi-day events
        if (!Schema::hasTable('event_daily_qrs')) {
            Schema::create('event_daily_qrs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id')->index();
                $table->date('qr_date');                        // The calendar date this QR is valid for
                $table->tinyInteger('day_number');              // 1-based (Day 1, Day 2, ...)
                $table->string('token', 64)->unique();          // random hex token embedded in QR
                $table->string('qr_image')->nullable();         // storage path of QR image
                $table->timestamps();

                $table->unique(['event_id', 'qr_date']);        // one QR per event per day
                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            });
        }

        // Per-day attendance records (one row per user per event per day)
        if (!Schema::hasTable('event_daily_attendances')) {
            Schema::create('event_daily_attendances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_registration_id')->index();
                $table->unsignedBigInteger('event_daily_qr_id')->index();
                $table->date('attendance_date');
                $table->tinyInteger('day_number');
                $table->timestamp('scanned_at');
                $table->timestamps();

                $table->unique(['event_registration_id', 'attendance_date'], 'eda_reg_date_unique'); // one scan per user per day
                $table->foreign('event_registration_id')->references('id')->on('event_registrations')->onDelete('cascade');
                $table->foreign('event_daily_qr_id')->references('id')->on('event_daily_qrs')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('event_daily_attendances');
        Schema::dropIfExists('event_daily_qrs');
    }
};
