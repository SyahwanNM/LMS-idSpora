<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('event_registrations') && !Schema::hasColumn('event_registrations', 'has_link_feedback')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                $table->boolean('has_link_feedback')->default(false)->after('feedback_submitted_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('event_registrations') && Schema::hasColumn('event_registrations', 'has_link_feedback')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                $table->dropColumn('has_link_feedback');
            });
        }
    }
};