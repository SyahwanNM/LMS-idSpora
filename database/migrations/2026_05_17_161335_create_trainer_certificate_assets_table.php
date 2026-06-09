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
        Schema::create('trainer_certificate_assets', function (Blueprint $table) {
            $table->id();
            $table->morphs('certifiable'); //Bisa terhubung ke Event atau Course
            $table->string('type', 30); //logo or ttd
            $table->string('name')->nullable(); //nama yg ttd
            $table->string('position')->nullable(); //jabatan yg ttd
            $table->string('image_path'); // path ke file gambar
            $table->unsignedTinyInteger('order_no')->default(1); // urutan tampil di sertifikat

            $table->timestamps();

            $table->index(
                ['certifiable_type', 'certifiable_id', 'type'],
                'trainer_cert_assets_lookup_idx'
            );

            $table->unique(
                ['certifiable_type', 'certifiable_id', 'type', 'order_no'],
                'trainer_cert_assets_unique_order'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_certificate_assets');
    }
};
