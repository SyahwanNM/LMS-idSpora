<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('payments')) { return; }

        Schema::table('payments', function (Blueprint $table) {
            // Midtrans/snap-specific columns — drop if present
            if (Schema::hasColumn('payments', 'snap_token')) {
                $table->dropColumn('snap_token');
            }
            if (Schema::hasColumn('payments', 'snap_redirect_url')) {
                $table->dropColumn('snap_redirect_url');
            }

            // Common Midtrans notification/metadata
            if (Schema::hasColumn('payments', 'pdf_url')) {
                $table->dropColumn('pdf_url');
            }
            if (Schema::hasColumn('payments', 'raw_notification')) {
                $table->dropColumn('raw_notification');
            }

            // Transaction details
            if (Schema::hasColumn('payments', 'transaction_id')) {
                $table->dropColumn('transaction_id');
            }
            if (Schema::hasColumn('payments', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
            if (Schema::hasColumn('payments', 'bank')) {
                $table->dropColumn('bank');
            }
            if (Schema::hasColumn('payments', 'va_number')) {
                $table->dropColumn('va_number');
            }
            if (Schema::hasColumn('payments', 'gross_amount')) {
                $table->dropColumn('gross_amount');
            }
            if (Schema::hasColumn('payments', 'fraud_status')) {
                $table->dropColumn('fraud_status');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('payments')) { return; }

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('order_id');
            }
            if (!Schema::hasColumn('payments', 'payment_type')) {
                $table->string('payment_type')->nullable();
            }
            if (!Schema::hasColumn('payments', 'bank')) {
                $table->string('bank')->nullable();
            }
            if (!Schema::hasColumn('payments', 'va_number')) {
                $table->string('va_number')->nullable();
            }
            if (!Schema::hasColumn('payments', 'gross_amount')) {
                $table->integer('gross_amount')->default(0);
            }
            if (!Schema::hasColumn('payments', 'fraud_status')) {
                $table->string('fraud_status')->nullable();
            }
            if (!Schema::hasColumn('payments', 'pdf_url')) {
                $table->string('pdf_url')->nullable();
            }
            if (!Schema::hasColumn('payments', 'raw_notification')) {
                $table->json('raw_notification')->nullable();
            }
            if (!Schema::hasColumn('payments', 'snap_token')) {
                $table->string('snap_token')->nullable();
            }
            if (!Schema::hasColumn('payments', 'snap_redirect_url')) {
                $table->string('snap_redirect_url')->nullable();
            }
        });
    }
};
