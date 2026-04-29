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
        Schema::table('courses', function (Blueprint $table) {
            $table->string('certificate_template')->nullable()->after('status');
            $table->longText('certificate_logo')->nullable()->after('certificate_template');
            $table->longText('certificate_signature')->nullable()->after('certificate_logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['certificate_template', 'certificate_logo', 'certificate_signature']);
        });
    }
};
