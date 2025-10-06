<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if(!Schema::hasColumn('events','ended_at')){
                $table->timestamp('ended_at')->nullable()->after('event_time');
            }
            if(!Schema::hasColumn('events','deleted_at')){
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if(Schema::hasColumn('events','ended_at')){
                $table->dropColumn('ended_at');
            }
            if(Schema::hasColumn('events','deleted_at')){
                $table->dropSoftDeletes();
            }
        });
    }
};
