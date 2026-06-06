<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->date('expense_date')->nullable();
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('category')->nullable();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('trainer_payments')) {
            Schema::create('trainer_payments', function (Blueprint $table) {
                $table->id();

                if (Schema::hasTable('users')) {
                    $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
                } else {
                    $table->unsignedBigInteger('trainer_id')->nullable();
                }

                $table->decimal('amount', 15, 2)->default(0);
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('event_expenses')) {
            Schema::create('event_expenses', function (Blueprint $table) {
                $table->id();

                if (Schema::hasTable('events')) {
                    $table->foreignId('event_id')->nullable()->constrained('events')->nullOnDelete();
                } else {
                    $table->unsignedBigInteger('event_id')->nullable();
                }

                $table->string('description')->nullable();
                $table->decimal('total', 15, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('event_expenses');
        Schema::dropIfExists('trainer_payments');
        Schema::dropIfExists('expenses');
    }
};