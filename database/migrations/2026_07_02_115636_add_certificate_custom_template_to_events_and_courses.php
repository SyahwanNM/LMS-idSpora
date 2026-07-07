<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->json('certificate_custom_template')->nullable()->after('certificate_template');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->json('certificate_custom_template')->nullable()->after('certificate_template');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('certificate_custom_template');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('certificate_custom_template');
        });
    }
};
