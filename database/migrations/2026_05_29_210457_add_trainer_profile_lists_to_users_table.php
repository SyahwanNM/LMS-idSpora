<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('trainer_skills')->nullable();
            $table->json('trainer_experiences')->nullable();
            $table->json('trainer_educations')->nullable();
            $table->json('trainer_certifications')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'trainer_skills',
                'trainer_experiences',
                'trainer_educations',
                'trainer_certifications'
            ]);
        });
    }
};
