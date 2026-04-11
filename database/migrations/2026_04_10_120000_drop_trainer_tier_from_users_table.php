<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'trainer_tier')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('trainer_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users') || Schema::hasColumn('users', 'trainer_tier')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('trainer_tier', 20)->default('associate')->after('late_uploads');
        });
    }
};
