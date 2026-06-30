<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\EventExpense;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete any EventExpense records where event is soft deleted or non-existent
        EventExpense::whereHas('event', function ($query) {
            $query->onlyTrashed();
        })->orWhereDoesntHave('event')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
