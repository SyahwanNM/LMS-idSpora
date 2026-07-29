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
            $table->string('certificate_signature')->nullable()->after('speaker');
            $table->string('certificate_logo')->nullable();
            $table->string('certificate_template')->default('template_1')->after('certificate_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('certificate_signature');
            $table->dropColumn('certificate_logo');
            $table->dropColumn('certificate_template');
        });
    }
};
