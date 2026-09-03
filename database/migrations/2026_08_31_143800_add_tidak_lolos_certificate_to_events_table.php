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
            if (!Schema::hasColumn('events', 'certificate_template_tidak_lolos')) {
                $table->string('certificate_template_tidak_lolos')->nullable()->after('certificate_template');
            }
            if (!Schema::hasColumn('events', 'certificate_logo_tidak_lolos')) {
                $table->longText('certificate_logo_tidak_lolos')->nullable()->after('certificate_logo');
            }
            if (!Schema::hasColumn('events', 'certificate_signature_tidak_lolos')) {
                $table->longText('certificate_signature_tidak_lolos')->nullable()->after('certificate_signature');
            }
            if (!Schema::hasColumn('events', 'file_tambahan_tidak_lolos')) {
                $table->string('file_tambahan_tidak_lolos')->nullable()->after('file_tambahan');
            }
            if (!Schema::hasColumn('events', 'certificate_custom_template_tidak_lolos')) {
                $table->json('certificate_custom_template_tidak_lolos')->nullable()->after('certificate_custom_template');
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
                'certificate_template_tidak_lolos',
                'certificate_logo_tidak_lolos',
                'certificate_signature_tidak_lolos',
                'file_tambahan_tidak_lolos',
                'certificate_custom_template_tidak_lolos',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('events', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
