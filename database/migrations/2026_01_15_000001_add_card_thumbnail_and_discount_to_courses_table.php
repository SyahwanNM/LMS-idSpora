<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('card_thumbnail')->nullable();
            $table->unsignedTinyInteger('discount_percent')->nullable()->after('price');
            $table->date('discount_start')->nullable()->after('discount_percent');
            $table->date('discount_end')->nullable()->after('discount_start');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['card_thumbnail', 'discount_percent', 'discount_start', 'discount_end']);
        });
    }
};
