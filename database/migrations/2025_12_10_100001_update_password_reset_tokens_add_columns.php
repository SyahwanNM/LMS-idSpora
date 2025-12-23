<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (!Schema::hasColumn('password_reset_tokens', 'verification_code')) {
                $table->string('verification_code', 6)->nullable()->after('token');
            }
            if (!Schema::hasColumn('password_reset_tokens', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('verification_code');
            }
            if (!Schema::hasColumn('password_reset_tokens', 'is_used')) {
                $table->boolean('is_used')->default(false)->after('expires_at');
            }
            if (!Schema::hasColumn('password_reset_tokens', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (Schema::hasColumn('password_reset_tokens', 'verification_code')) {
                $table->dropColumn('verification_code');
            }
            if (Schema::hasColumn('password_reset_tokens', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
            if (Schema::hasColumn('password_reset_tokens', 'is_used')) {
                $table->dropColumn('is_used');
            }
            if (Schema::hasColumn('password_reset_tokens', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};
