<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('trainer_certificate_assets')) {
            return;
        }

        Schema::create('trainer_certificate_assets', function (Blueprint $table) {
            $table->id();

            $table->string('certifiable_type');
            $table->unsignedBigInteger('certifiable_id');

            $table->enum('type', [
                'logo',
                'signature',
            ]);

            $table->string('name')->nullable();
            $table->integer('position')->nullable();
            $table->string('image_path');
            $table->integer('order_no')->default(1);

            $table->timestamps();

            $table->index([
                'certifiable_type',
                'certifiable_id',
            ]);
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('trainer_certificate_assets');
    }
};