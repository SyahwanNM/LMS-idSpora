<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('user_saved_events')) {
            Schema::create('user_saved_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('event_id');
                $table->timestamps();
                $table->unique(['user_id','event_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_saved_events');
    }
};
