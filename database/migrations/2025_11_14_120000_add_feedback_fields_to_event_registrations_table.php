<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            if(!Schema::hasColumn('event_registrations','feedback_text')){
                $table->text('feedback_text')->nullable()->after('registration_code');
            }
            if(!Schema::hasColumn('event_registrations','feedback_submitted_at')){
                $table->timestamp('feedback_submitted_at')->nullable()->after('feedback_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            if(Schema::hasColumn('event_registrations','feedback_submitted_at')){
                $table->dropColumn('feedback_submitted_at');
            }
            if(Schema::hasColumn('event_registrations','feedback_text')){
                $table->dropColumn('feedback_text');
            }
        });
    }
};
