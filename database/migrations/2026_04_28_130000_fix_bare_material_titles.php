<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $concatSql = DB::getDriverName() === 'sqlite'
            ? "'Module ' || order_no || ' - Material'"
            : "CONCAT('Module ', order_no, ' - Material')";

        // Fix modules with bare "Material" title (no Module N prefix)
        DB::table('course_module')
            ->where('title', 'Material')
            ->update([
                'title' => DB::raw($concatSql)
            ]);

        DB::table('course_template_modules')
            ->where('title', 'Material')
            ->update([
                'title' => DB::raw($concatSql)
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
