<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix modules with bare "Material" title (no Module N prefix)
        DB::table('course_module')
            ->where('title', 'Material')
            ->update([
                'title' => DB::raw("CONCAT('Module ', order_no, ' - Material')")
            ]);

        DB::table('course_template_modules')
            ->where('title', 'Material')
            ->update([
                'title' => DB::raw("CONCAT('Module ', order_no, ' - Material')")
            ]);
    }

    public function down(): void
    {
        // Rollback: strip Module N prefix
        DB::table('course_module')
            ->where('title', 'like', 'Module % - Material')
            ->update(['title' => 'Material']);

        DB::table('course_template_modules')
            ->where('title', 'like', 'Module % - Material')
            ->update(['title' => 'Material']);
    }
};
