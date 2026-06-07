<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update course_module table
        DB::table('course_module')
            ->where('title', 'like', '%PDF Material%')
            ->update([
                'title' => DB::raw("REPLACE(title, 'PDF Material', 'Material')")
            ]);

        // Update course_template_modules table
        DB::table('course_template_modules')
            ->where('title', 'like', '%PDF Material%')
            ->update([
                'title' => DB::raw("REPLACE(title, 'PDF Material', 'Material')")
            ]);
    }

    public function down(): void
    {
        // Rollback: restore "PDF Material"
        DB::table('course_module')
            ->where('title', 'like', '%Material%')
            ->whereNotLike('title', '%PDF Material%')
            ->update([
                'title' => DB::raw("REPLACE(title, 'Material', 'PDF Material')")
            ]);

        DB::table('course_template_modules')
            ->where('title', 'like', '%Material%')
            ->whereNotLike('title', '%PDF Material%')
            ->update([
                'title' => DB::raw("REPLACE(title, 'Material', 'PDF Material')")
            ]);
    }
};
