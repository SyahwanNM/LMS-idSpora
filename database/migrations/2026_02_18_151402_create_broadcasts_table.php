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
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('segment'); // all, reseller, trainer, inactive
            $table->string('platform'); // email, whatsapp, both
            $table->unsignedBigInteger('sender_id');
            $table->integer('target_count')->default(0);
            $table->string('status')->default('sent'); // sent, draft, failed
            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
    }
};
