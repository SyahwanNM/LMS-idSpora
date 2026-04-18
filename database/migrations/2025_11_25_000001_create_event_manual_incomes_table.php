<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		if (!Schema::hasTable('event_manual_incomes')) {
			Schema::create('event_manual_incomes', function (Blueprint $table) {
				$table->id();
				$table->foreignId('event_id')->constrained('events')->onDelete('cascade');
				$table->string('item');
				$table->unsignedBigInteger('amount')->default(0);
				$table->text('note')->nullable();
				$table->timestamps();
			});
		}
	}

	public function down(): void
	{
		Schema::dropIfExists('event_manual_incomes');
	}
};
