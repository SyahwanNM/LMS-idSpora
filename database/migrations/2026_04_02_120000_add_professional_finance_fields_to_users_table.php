<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'academic_title')) {
                $table->string('academic_title', 120)->nullable()->after('name');
            }

            if (!Schema::hasColumn('users', 'linkedin_url')) {
                $table->string('linkedin_url', 255)->nullable()->after('website');
            }

            if (!Schema::hasColumn('users', 'bank_name')) {
                $table->string('bank_name', 120)->nullable()->after('linkedin_url');
            }

            if (!Schema::hasColumn('users', 'bank_account_number')) {
                $table->string('bank_account_number', 60)->nullable()->after('bank_name');
            }

            if (!Schema::hasColumn('users', 'bank_account_holder')) {
                $table->string('bank_account_holder', 150)->nullable()->after('bank_account_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'bank_account_holder')) {
                $table->dropColumn('bank_account_holder');
            }

            if (Schema::hasColumn('users', 'bank_account_number')) {
                $table->dropColumn('bank_account_number');
            }

            if (Schema::hasColumn('users', 'bank_name')) {
                $table->dropColumn('bank_name');
            }

            if (Schema::hasColumn('users', 'linkedin_url')) {
                $table->dropColumn('linkedin_url');
            }

            if (Schema::hasColumn('users', 'academic_title')) {
                $table->dropColumn('academic_title');
            }
        });
    }
};