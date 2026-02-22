<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // ..._create_referrals_table.php
    public function up() : void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // Siapa yang dapet komisi (Reseller)
            $table->foreignId('referred_user_id')->constrained('users'); // Siapa yang diajak (Pembeli)
            $table->decimal('amount', 15, 2); // Jumlah komisi
            $table->string('status')->default('pending'); // pending, paid, canceled
            $table->string('description')->nullable(); // Misal: "Komisi Web Vol 2"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
