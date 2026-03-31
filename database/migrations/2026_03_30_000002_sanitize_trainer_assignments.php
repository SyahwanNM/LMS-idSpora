<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('courses', 'trainer_id')) {
            DB::table('courses')
                ->whereNotNull('trainer_id')
                ->whereNotIn('trainer_id', function ($query) {
                    $query->select('id')
                        ->from('users')
                        ->whereRaw('LOWER(role) = ?', ['trainer']);
                })
                ->update(['trainer_id' => null]);
        }

        if (Schema::hasColumn('events', 'trainer_id')) {
            DB::table('events')
                ->whereNotNull('trainer_id')
                ->whereNotIn('trainer_id', function ($query) {
                    $query->select('id')
                        ->from('users')
                        ->whereRaw('LOWER(role) = ?', ['trainer']);
                })
                ->update(['trainer_id' => null]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank: this migration performs data cleanup.
    }
};
