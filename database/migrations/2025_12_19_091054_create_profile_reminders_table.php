<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profile_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('last_shown_at')->nullable()->comment('Timestamp terakhir reminder ditampilkan');
            $table->integer('dismiss_count')->default(0)->comment('Jumlah kali user dismiss reminder (maksimal 2)');
            $table->boolean('is_active')->default(true)->comment('Apakah reminder masih aktif');
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_reminders');
    }
};
