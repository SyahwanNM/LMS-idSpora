<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('event_registrations')) { return; }

        Schema::table('event_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('event_registrations', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('payment_url');
            }
            if (!Schema::hasColumn('event_registrations', 'payment_verified_at')) {
                $table->timestamp('payment_verified_at')->nullable()->after('payment_proof');
            }
            if (!Schema::hasColumn('event_registrations', 'payment_verified_by')) {
                $table->foreignId('payment_verified_by')->nullable()->constrained('users')->onDelete('set null')->after('payment_verified_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('event_registrations')) { return; }

        Schema::table('event_registrations', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('event_registrations', 'payment_verified_by')) {
                // dropping foreign key will be handled by dropColumn in most DB engines
                $drops[] = 'payment_verified_by';
            }
            if (Schema::hasColumn('event_registrations', 'payment_verified_at')) {
                $drops[] = 'payment_verified_at';
            }
            if (Schema::hasColumn('event_registrations', 'payment_proof')) {
                $drops[] = 'payment_proof';
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
