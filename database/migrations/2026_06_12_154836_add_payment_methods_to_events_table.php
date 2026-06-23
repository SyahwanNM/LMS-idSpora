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
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'accept_online_payment')) {
                $table->boolean('accept_online_payment')->default(true);
            }
            if (!Schema::hasColumn('events', 'accept_manual_transfer')) {
                $table->boolean('accept_manual_transfer')->default(true);
            }
            if (!Schema::hasColumn('events', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable();
            }
            if (!Schema::hasColumn('events', 'bank_name')) {
                $table->string('bank_name')->nullable();
            }
            if (!Schema::hasColumn('events', 'bank_account_holder')) {
                $table->string('bank_account_holder')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('events', 'accept_online_payment')) {
                $columns[] = 'accept_online_payment';
            }
            if (Schema::hasColumn('events', 'accept_manual_transfer')) {
                $columns[] = 'accept_manual_transfer';
            }
            if (Schema::hasColumn('events', 'bank_account_number')) {
                $columns[] = 'bank_account_number';
            }
            if (Schema::hasColumn('events', 'bank_name')) {
                $columns[] = 'bank_name';
            }
            if (Schema::hasColumn('events', 'bank_account_holder')) {
                $columns[] = 'bank_account_holder';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
