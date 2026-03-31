<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Change enum to include 'trainer' role
            $table->enum('role', ['admin', 'user', 'trainer'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert to original enum without 'trainer'
            $table->enum('role', ['admin', 'user'])->change();
        });
    }
};
