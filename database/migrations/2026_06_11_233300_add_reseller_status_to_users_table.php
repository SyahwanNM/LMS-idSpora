<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('reseller_status', ['active', 'suspended'])->default('active')->after('user_status');
        });

        // Copy existing reseller status from user_status
        // If a reseller was suspended, set reseller_status to suspended
        DB::table('users')
            ->whereNotNull('referral_code')
            ->where('user_status', 'suspended')
            ->update([
                'reseller_status' => 'suspended'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('reseller_status');
        });
    }
};
