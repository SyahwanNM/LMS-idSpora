<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'snap_token')) {
                    $table->string('snap_token')->nullable()->after('order_id');
                }
                if (!Schema::hasColumn('payments', 'snap_redirect_url')) {
                    $table->string('snap_redirect_url')->nullable()->after('snap_token');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (Schema::hasColumn('payments', 'snap_token')) {
                    $table->dropColumn('snap_token');
                }
                if (Schema::hasColumn('payments', 'snap_redirect_url')) {
                    $table->dropColumn('snap_redirect_url');
                }
            });
        }
    }
};
