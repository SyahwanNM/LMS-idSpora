<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainer_payments', function (Blueprint $table) {
            $table->date('payment_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('trainer_payments', function (Blueprint $table) {
            $table->date('payment_date')->nullable(false)->change();
        });
    }
};
