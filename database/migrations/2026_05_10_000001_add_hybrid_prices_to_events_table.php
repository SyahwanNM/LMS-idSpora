<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'price_offline')) {
                $table->decimal('price_offline', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('events', 'price_online')) {
                $table->decimal('price_online', 10, 2)->nullable()->after('price_offline');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'price_offline')) {
                $table->dropColumn('price_offline');
            }
            if (Schema::hasColumn('events', 'price_online')) {
                $table->dropColumn('price_online');
            }
        });
    }
};
