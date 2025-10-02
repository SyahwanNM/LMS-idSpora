<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('certificate_number')->nullable()->unique()->after('registration_code');
            $table->timestamp('certificate_issued_at')->nullable()->after('certificate_number');
        });
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn(['certificate_number','certificate_issued_at']);
        });
    }
};
