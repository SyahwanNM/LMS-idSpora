<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('attendance_status')->nullable()->after('status'); // 'yes' or 'no'
            $table->timestamp('attended_at')->nullable()->after('attendance_status');
        });
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn(['attendance_status','attended_at']);
        });
    }
};
