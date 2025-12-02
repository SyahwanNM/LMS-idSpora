<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if(!Schema::hasTable('event_expenses')){
            Schema::create('event_expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
                $table->string('item');
                $table->unsignedInteger('quantity')->default(0);
                $table->unsignedBigInteger('unit_price')->default(0);
                $table->unsignedBigInteger('total')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('event_expenses');
    }
};
