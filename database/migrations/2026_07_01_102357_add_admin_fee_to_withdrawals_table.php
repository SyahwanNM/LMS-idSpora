<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            if (!Schema::hasColumn('withdrawals', 'admin_fee')) {
                $table->decimal('admin_fee', 15, 2)->default(3000)->after('amount');
            }
            if (!Schema::hasColumn('withdrawals', 'net_amount')) {
                $table->decimal('net_amount', 15, 2)->nullable()->after('admin_fee');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            if (Schema::hasColumn('withdrawals', 'admin_fee')) {
                $table->dropColumn('admin_fee');
            }
            if (Schema::hasColumn('withdrawals', 'net_amount')) {
                $table->dropColumn('net_amount');
            }
        });
    }
};
