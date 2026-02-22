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
        if (!Schema::hasColumn('events', 'price')) {
            Schema::table('events', function (Blueprint $table) {
                $table->decimal('price', 10, 2)->default(0)->after('location');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('events', 'price')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }
    }
};
