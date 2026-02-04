<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // If duplicates already exist, remove them first to avoid migration failure.
        if (Schema::hasTable('progress')) {
            $duplicates = DB::table('progress')
                ->select('enrollment_id', 'course_module_id', DB::raw('COUNT(*) as cnt'))
                ->groupBy('enrollment_id', 'course_module_id')
                ->having('cnt', '>', 1)
                ->get();

            foreach ($duplicates as $dup) {
                $ids = DB::table('progress')
                    ->where('enrollment_id', $dup->enrollment_id)
                    ->where('course_module_id', $dup->course_module_id)
                    ->orderByDesc('completed')
                    ->orderByDesc('updated_at')
                    ->orderByDesc('id')
                    ->pluck('id')
                    ->all();

                // Keep the first row, delete the rest
                $keepId = $ids[0] ?? null;
                if ($keepId) {
                    DB::table('progress')->whereIn('id', array_slice($ids, 1))->delete();
                }
            }
        }

        Schema::table('progress', function (Blueprint $table) {
            // Prevent duplicate progress rows for the same enrollment + module
            $table->unique(['enrollment_id', 'course_module_id'], 'progress_enrollment_module_unique');
        });
    }

    public function down(): void
    {
        Schema::table('progress', function (Blueprint $table) {
            $table->dropUnique('progress_enrollment_module_unique');
        });
    }
};
