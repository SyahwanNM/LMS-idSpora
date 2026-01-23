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
        Schema::create('carousels', function (Blueprint $table) {
            $table->id();
            $table->string('location')->comment('dashboard, event, course, landing'); // Lokasi carousel
            $table->string('title')->nullable(); // Judul/alt text untuk gambar
            $table->string('image_path'); // Path gambar
            $table->text('link_url')->nullable(); // URL jika gambar bisa diklik
            $table->integer('order')->default(0); // Urutan tampil
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carousels');
    }
};
