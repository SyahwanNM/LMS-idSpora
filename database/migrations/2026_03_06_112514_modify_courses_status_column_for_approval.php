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
        // Modify status column to support approval workflow
        Schema::table('courses', function (Blueprint $table) {
            // Drop the old enum column
            $table->dropColumn('status');
        });

        Schema::table('courses', function (Blueprint $table) {
            // Add new status column with approval statuses
            $table->enum('status', ['pending_review', 'approved', 'rejected', 'active', 'archive'])
                ->default('pending_review')
                ->after('duration');

            // Add columns for approval tracking
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['rejection_reason', 'approved_at', 'rejected_at', 'approved_by']);
            $table->dropColumn('status');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->enum('status', ['active', 'archive'])->default('active')->after('duration');
        });
    }
};
