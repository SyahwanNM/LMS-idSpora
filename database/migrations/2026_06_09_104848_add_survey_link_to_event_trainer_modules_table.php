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
        if (!Schema::hasColumn('event_trainer_modules', 'survey_link')) {
            Schema::table('event_trainer_modules', function (Blueprint $table) {
                $table->string('survey_link', 2048)->nullable()->after('path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('event_trainer_modules', 'survey_link')) {
            Schema::table('event_trainer_modules', function (Blueprint $table) {
                $table->dropColumn('survey_link');
            });
        }
    }
};
