<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('payments')) { return; }
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'order_id')) {
                $table->string('order_id')->after('event_id');
                $table->unique('order_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('payments')) { return; }
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'order_id')) {
                $table->dropUnique(['order_id']);
                $table->dropColumn('order_id');
            }
        });
    }
};
