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
            if (!Schema::hasColumn('events', 'finalist_payment_start')) {
                $table->dateTime('finalist_payment_start')->nullable()->after('price_stage2');
            }
            if (!Schema::hasColumn('events', 'finalist_payment_end')) {
                $table->dateTime('finalist_payment_end')->nullable()->after('finalist_payment_start');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'finalist_payment_start')) {
                $table->dropColumn('finalist_payment_start');
            }
            if (Schema::hasColumn('events', 'finalist_payment_end')) {
                $table->dropColumn('finalist_payment_end');
            }
        });
    }
};
