<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Find and remove duplicate certificate_numbers, keeping only the first/oldest one
        $duplicates = DB::table('trainer_certificates')
            ->select('certificate_number')
            ->groupBy('certificate_number')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('certificate_number');

        foreach ($duplicates as $certNum) {
            $ids = DB::table('trainer_certificates')
                ->where('certificate_number', $certNum)
                ->orderBy('created_at', 'asc')
                ->pluck('id')
                ->toArray();

            // Keep the first one, delete the rest
            if (count($ids) > 1) {
                array_shift($ids); // Remove first item
                DB::table('trainer_certificates')
                    ->whereIn('id', $ids)
                    ->delete();
            }
        }
    }

    public function down(): void
    {
        // No rollback available as duplicates are deleted
    }
};
