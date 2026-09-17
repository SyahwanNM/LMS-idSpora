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
            if (!Schema::hasColumn('events', 'certificate_template_pemenang')) {
                $table->string('certificate_template_pemenang')->nullable()->after('certificate_template_tidak_lolos');
            }
            if (!Schema::hasColumn('events', 'certificate_logo_pemenang')) {
                $table->longText('certificate_logo_pemenang')->nullable()->after('certificate_logo_tidak_lolos');
            }
            if (!Schema::hasColumn('events', 'certificate_signature_pemenang')) {
                $table->longText('certificate_signature_pemenang')->nullable()->after('certificate_signature_tidak_lolos');
            }
            if (!Schema::hasColumn('events', 'file_tambahan_pemenang')) {
                $table->string('file_tambahan_pemenang')->nullable()->after('file_tambahan_tidak_lolos');
            }
            if (!Schema::hasColumn('events', 'certificate_custom_template_pemenang')) {
                $table->json('certificate_custom_template_pemenang')->nullable()->after('certificate_custom_template_tidak_lolos');
            }
            if (!Schema::hasColumn('events', 'certificate_winner_ids')) {
                $table->json('certificate_winner_ids')->nullable()->after('certificate_custom_template_pemenang');
            }
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('event_registrations', 'is_winner')) {
                $table->boolean('is_winner')->default(false)->after('status');
            }
            if (!Schema::hasColumn('event_registrations', 'winner_title')) {
                $table->string('winner_title')->nullable()->after('is_winner');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $cols = [
                'certificate_template_pemenang',
                'certificate_logo_pemenang',
                'certificate_signature_pemenang',
                'file_tambahan_pemenang',
                'certificate_custom_template_pemenang',
                'certificate_winner_ids',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('events', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $cols = ['is_winner', 'winner_title'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('event_registrations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
