<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if(!Schema::hasColumn('events','latitude')) $table->decimal('latitude', 10, 7)->nullable()->after('maps_url');
            if(!Schema::hasColumn('events','longitude')) $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if(Schema::hasColumn('events','latitude')) $table->dropColumn('latitude');
            if(Schema::hasColumn('events','longitude')) $table->dropColumn('longitude');
        });
    }
};
