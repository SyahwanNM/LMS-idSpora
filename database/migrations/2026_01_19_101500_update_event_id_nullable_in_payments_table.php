<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('payments')) { return; }
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // You may want to revert to NOT NULL, but be careful with existing data
    }
};
