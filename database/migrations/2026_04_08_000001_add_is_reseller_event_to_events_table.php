<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'is_reseller_event')) {
                if (Schema::hasColumn('events', 'is_published')) {
                    $table->boolean('is_reseller_event')->default(false)->after('is_published');
                } else {
                    $table->boolean('is_reseller_event')->default(false);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'is_reseller_event')) {
                $table->dropColumn('is_reseller_event');
            }
        });
    }
};
