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
        Schema::table('event_schedule_items', function (Blueprint $table) {
            $table->string('start')->nullable()->change();
            $table->string('end')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_schedule_items', function (Blueprint $table) {
            $table->time('start')->nullable()->change();
            $table->time('end')->nullable()->change();
        });
    }
};
