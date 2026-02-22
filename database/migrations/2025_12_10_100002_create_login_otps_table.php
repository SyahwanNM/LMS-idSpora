<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('login_otps')) {
            Schema::create('login_otps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('email');
                $table->string('code', 6);
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_used')->default(false);
                $table->timestamps();
                $table->index(['email','is_used']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('login_otps');
    }
};
