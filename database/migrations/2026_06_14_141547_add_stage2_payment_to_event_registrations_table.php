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
        Schema::table('event_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('event_registrations', 'stage2_payment_status')) {
                $table->string('stage2_payment_status', 50)->default('not_required')->after('submission_notes');
            }
            if (!Schema::hasColumn('event_registrations', 'stage2_payment_at')) {
                $table->timestamp('stage2_payment_at')->nullable()->after('stage2_payment_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('event_registrations', 'stage2_payment_status')) {
                $table->dropColumn('stage2_payment_status');
            }
            if (Schema::hasColumn('event_registrations', 'stage2_payment_at')) {
                $table->dropColumn('stage2_payment_at');
            }
        });
    }
};
