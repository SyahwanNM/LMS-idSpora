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
        Schema::create('trainer_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('invitation_notification_id')
                ->nullable()
                ->constrained('trainer_notifications')
                ->nullOnDelete();

            // Scheme selection: 1 (35% - Full), 2 (25% - Medium), 3 (10% - Light)
            $table->unsignedTinyInteger('scheme_type')->nullable();

            // Legal agreement tracking
            $table->timestamp('legal_agreement_accepted_at')->nullable();
            $table->string('legal_agreement_accepted_ip', 45)->nullable(); // IPv6 friendly
            $table->string('legal_agreement_accepted_user_agent')->nullable();

            // SLA timer for material upload (72 hours from acceptance)
            $table->timestamp('sla_upload_deadline')->nullable();
            $table->timestamp('materials_uploaded_at')->nullable();

            // Status tracking
            $table->enum('status', ['pending', 'accepted', 'completed', 'rejected', 'expired'])
                ->default('pending')
                ->index();

            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Metadata
            $table->timestamps();
            $table->softDeletes();

            // Indexes for efficient querying
            $table->index(['trainer_id', 'event_id']);
            $table->index(['trainer_id', 'status']);
            $table->index(['event_id', 'status']);
            $table->index('sla_upload_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_assignments');
    }
};
