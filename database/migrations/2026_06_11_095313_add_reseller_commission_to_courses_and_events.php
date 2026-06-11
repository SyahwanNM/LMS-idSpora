<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('reseller_commission_bronze')->default(10)->after('is_reseller_course');
            $table->integer('reseller_commission_silver')->default(12)->after('reseller_commission_bronze');
            $table->integer('reseller_commission_gold')->default(15)->after('reseller_commission_silver');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->integer('reseller_commission_bronze')->default(10)->after('is_reseller_event');
            $table->integer('reseller_commission_silver')->default(12)->after('reseller_commission_bronze');
            $table->integer('reseller_commission_gold')->default(15)->after('reseller_commission_silver');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['reseller_commission_bronze', 'reseller_commission_silver', 'reseller_commission_gold']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['reseller_commission_bronze', 'reseller_commission_silver', 'reseller_commission_gold']);
        });
    }
};
