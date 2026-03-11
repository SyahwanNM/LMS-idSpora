<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventManualIncomesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('event_manual_incomes', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('event_id')->nullable();
			$table->string('title')->nullable();
			$table->text('description')->nullable();
			$table->integer('amount')->default(0);
			$table->timestamps();
		});
	}
}