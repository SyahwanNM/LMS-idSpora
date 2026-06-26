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
        Schema::table('event_trainer_modules', function (Blueprint $table) {
            if (!Schema::hasColumn('event_trainer_modules', 'logo_stamped')) {
                $table->boolean('logo_stamped')->default(false)->after('status');
            }
        });

        Schema::table('trainer_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('trainer_assignments', 'logo_stamped')) {
                $table->boolean('logo_stamped')->default(false)->after('material_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_trainer_modules', function (Blueprint $table) {
            if (Schema::hasColumn('event_trainer_modules', 'logo_stamped')) {
                $table->dropColumn('logo_stamped');
            }
        });

        Schema::table('trainer_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('trainer_assignments', 'logo_stamped')) {
                $table->dropColumn('logo_stamped');
            }
        });
    }
};
