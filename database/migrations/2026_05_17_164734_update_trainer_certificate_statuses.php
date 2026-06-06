<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('trainer_certificates')
            ->where('status', 'sent')
            ->update([
                'status' => 'published',
            ]);
    }

    public function down(): void
    {
        DB::table('trainer_certificates')
            ->where('status', 'published')
            ->update([
                'status' => 'sent',
            ]);
    }
};