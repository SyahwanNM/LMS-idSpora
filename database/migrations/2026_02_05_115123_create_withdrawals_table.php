<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // ..._create_withdrawals_table.php
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Link ke tabel user
            $table->decimal('amount', 15, 2); // Jumlah penarikan
            $table->string('bank_name'); // Nama Bank (BCA, Mandiri, dll)
            $table->string('account_number'); // Nomor Rekening
            $table->string('account_holder'); // Atas Nama
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Status default 'pending'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
