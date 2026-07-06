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
<<<<<<< HEAD
            $table->string('full_name')->nullable()->after('user_id');
            $table->string('whatsapp_number')->nullable()->after('full_name');
            $table->string('institution_location')->nullable()->after('university_origin');
            $table->string('info_source')->nullable()->after('position');
            $table->string('educational_background')->nullable()->after('info_source');
=======
            if (!Schema::hasColumn('event_registrations', 'full_name')) {
                $table->string('full_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('event_registrations', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable()->after('full_name');
            }
            if (!Schema::hasColumn('event_registrations', 'institution_location')) {
                $table->string('institution_location')->nullable()->after('university_origin');
            }
            if (!Schema::hasColumn('event_registrations', 'info_source')) {
                $table->string('info_source')->nullable()->after('position');
            }
            if (!Schema::hasColumn('event_registrations', 'educational_background')) {
                $table->string('educational_background')->nullable()->after('info_source');
            }
>>>>>>> b863fb54e2abec006fb54479f68889751e33734a
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
<<<<<<< HEAD
            $table->dropColumn([
=======
            $columns = [
>>>>>>> b863fb54e2abec006fb54479f68889751e33734a
                'full_name',
                'whatsapp_number',
                'institution_location',
                'info_source',
                'educational_background'
<<<<<<< HEAD
            ]);
=======
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('event_registrations', $col)) {
                    $table->dropColumn($col);
                }
            }
>>>>>>> b863fb54e2abec006fb54479f68889751e33734a
        });
    }
};
