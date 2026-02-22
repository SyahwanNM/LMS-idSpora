<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'free_access_mode')) {
                // Controls how much of a free (price=0) course is accessible.
                // Values: 'all' | 'limit_2'
                $table->string('free_access_mode', 20)->default('limit_2')->after('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'free_access_mode')) {
                $table->dropColumn('free_access_mode');
            }
        });
    }
};
