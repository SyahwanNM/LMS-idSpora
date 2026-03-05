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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0)->after('order_id');
            }
            if (!Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('amount');
            }
            if (!Schema::hasColumn('payments', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('transaction_id');
            }
            if (!Schema::hasColumn('payments', 'bank')) {
                $table->string('bank')->nullable()->after('payment_type');
            }
            if (!Schema::hasColumn('payments', 'va_number')) {
                $table->string('va_number')->nullable()->after('bank');
            }
            if (!Schema::hasColumn('payments', 'fraud_status')) {
                $table->string('fraud_status')->nullable()->after('va_number');
            }
            if (!Schema::hasColumn('payments', 'pdf_url')) {
                $table->string('pdf_url')->nullable()->after('fraud_status');
            }
            if (!Schema::hasColumn('payments', 'raw_notification')) {
                $table->json('raw_notification')->nullable()->after('pdf_url');
            }
            if (!Schema::hasColumn('payments', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('raw_notification');
            }
            if (!Schema::hasColumn('payments', 'snap_redirect_url')) {
                $table->string('snap_redirect_url')->nullable()->after('snap_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'amount', 'transaction_id', 'payment_type', 'bank', 
                'va_number', 'fraud_status', 'pdf_url', 'raw_notification',
                'snap_token', 'snap_redirect_url'
            ]);
        });
    }
};
