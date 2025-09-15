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
        Schema::table('course_module', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->integer('duration')->default(0)->after('description'); // duration in minutes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_module', function (Blueprint $table) {
            $table->dropColumn(['description', 'duration']);
        });
    }
};