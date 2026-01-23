<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('payment_proofs')) { return; }

        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manual_payment_id')->nullable()->constrained('manual_payments')->onDelete('cascade');
            $table->foreignId('event_registration_id')->nullable()->constrained('event_registrations')->onDelete('cascade');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('payment_proofs')) { return; }
        Schema::dropIfExists('payment_proofs');
    }
};
