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
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('expense_date');
            $table->string('proof_of_payment')->nullable()->after('status');
            $table->string('rejected_reason')->nullable()->after('proof_of_payment');
        });

        Schema::table('trainer_payments', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('amount');
            $table->string('rejected_reason')->nullable()->after('status');
        });

        Schema::table('event_expenses', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('total');
            $table->string('proof_of_payment')->nullable()->after('status');
            $table->string('rejected_reason')->nullable()->after('proof_of_payment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['status', 'proof_of_payment', 'rejected_reason']);
        });

        Schema::table('trainer_payments', function (Blueprint $table) {
            $table->dropColumn(['status', 'rejected_reason']);
        });

        Schema::table('event_expenses', function (Blueprint $table) {
            $table->dropColumn(['status', 'proof_of_payment', 'rejected_reason']);
        });
    }
};
