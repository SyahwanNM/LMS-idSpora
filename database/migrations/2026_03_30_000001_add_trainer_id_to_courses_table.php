<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('courses', 'trainer_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->foreignId('trainer_id')
                    ->nullable()
                    ->after('category_id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        // Backfill legacy data when older schema stored trainer in user_id.
        if (Schema::hasColumn('courses', 'trainer_id') && Schema::hasColumn('courses', 'user_id')) {
            DB::table('courses')
                ->whereNull('trainer_id')
                ->whereNotNull('user_id')
                ->update(['trainer_id' => DB::raw('user_id')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('courses', 'trainer_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropConstrainedForeignId('trainer_id');
            });
        }
    }
};
