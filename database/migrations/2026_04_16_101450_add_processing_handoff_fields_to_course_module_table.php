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
        Schema::table('course_module', function (Blueprint $table) {
            $table->string('processing_status', 40)->nullable()->after('review_status');
            $table->unsignedBigInteger('assigned_by_admin_trainer_id')->nullable()->after('processing_status');
            $table->unsignedBigInteger('assigned_to_admin_course_id')->nullable()->after('assigned_by_admin_trainer_id');
            $table->timestamp('assigned_at')->nullable()->after('assigned_to_admin_course_id');
            $table->text('assignment_notes')->nullable()->after('assigned_at');

            $table->string('processed_file_url')->nullable()->after('assignment_notes');
            $table->string('processed_file_name')->nullable()->after('processed_file_url');
            $table->string('processed_mime', 120)->nullable()->after('processed_file_name');
            $table->unsignedBigInteger('processed_file_size')->nullable()->after('processed_mime');
            $table->timestamp('processed_at')->nullable()->after('processed_file_size');

            $table->unsignedInteger('processing_version')->default(0)->after('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_module', function (Blueprint $table) {
            $table->dropColumn([
                'processing_status',
                'assigned_by_admin_trainer_id',
                'assigned_to_admin_course_id',
                'assigned_at',
                'assignment_notes',
                'processed_file_url',
                'processed_file_name',
                'processed_mime',
                'processed_file_size',
                'processed_at',
                'processing_version',
            ]);
        });
    }
};
