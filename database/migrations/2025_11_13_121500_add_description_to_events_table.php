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
        if (!Schema::hasColumn('events', 'description')) {
            Schema::table('events', function (Blueprint $table) {
                // Use longText to safely store rich HTML from the editor
                $table->longText('description')->nullable()->after('speaker');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('events', 'description')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
