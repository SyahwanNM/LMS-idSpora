<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add material approval status to events table
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'material_status')) {
            Schema::table('events', function (Blueprint $table) {
                $table->enum('material_status', ['pending', 'pending_review', 'approved', 'rejected'])->default('pending')->after('module_path');
                $table->timestamp('material_approved_at')->nullable()->after('material_status');
                $table->foreignId('material_approved_by')->nullable()->constrained('users')->nullOnDelete()->after('material_approved_at');
                $table->text('material_rejection_reason')->nullable()->after('material_approved_by');
            });
        }

        // Add invitation_status to trainer_notifications table
        if (Schema::hasTable('trainer_notifications') && !Schema::hasColumn('trainer_notifications', 'invitation_status')) {
            Schema::table('trainer_notifications', function (Blueprint $table) {
                $table->string('invitation_status')->nullable()->default(null)->after('data');
                $table->timestamp('responded_at')->nullable()->after('invitation_status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (Schema::hasColumn('events', 'material_status')) {
                    $table->dropColumn(['material_status', 'material_approved_at', 'material_approved_by', 'material_rejection_reason']);
                }
            });
        }

        if (Schema::hasTable('trainer_notifications')) {
            Schema::table('trainer_notifications', function (Blueprint $table) {
                if (Schema::hasColumn('trainer_notifications', 'invitation_status')) {
                    $table->dropColumn(['invitation_status', 'responded_at']);
                }
            });
        }
    }
};
