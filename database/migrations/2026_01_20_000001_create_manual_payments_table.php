<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('manual_payments')) { return; }

        Schema::create('manual_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('cascade');
            $table->foreignId('event_registration_id')->nullable()->constrained('event_registrations')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('order_id')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('currency')->default('IDR');
            $table->string('method')->default('qris');
            $table->enum('status', ['pending', 'settled', 'rejected'])->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('manual_payments')) { return; }
        Schema::dropIfExists('manual_payments');
    }
};
