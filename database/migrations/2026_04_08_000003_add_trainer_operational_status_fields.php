<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_status', 20)->default('active')->after('trainer_tier');
            $table->unsignedInteger('consecutive_expired_invitations')->default(0)->after('user_status');
            $table->unsignedInteger('consecutive_late_uploads')->default(0)->after('consecutive_expired_invitations');
            $table->timestamp('last_teaching_at')->nullable()->after('consecutive_late_uploads');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->timestamp('trainer_e_agreement_accepted_at')->nullable()->after('trainer_scheme_accepted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('trainer_e_agreement_accepted_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'user_status',
                'consecutive_expired_invitations',
                'consecutive_late_uploads',
                'last_teaching_at',
            ]);
        });
    }
};
