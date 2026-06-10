<?php

namespace Tests\Feature;

use App\Models\TrainerNotification;
use App\Models\User;
use App\Services\TrainerActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainerOperationalFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pending_invitation_is_marked_expired_after_24_hours(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $trainer = User::factory()->create([
            'role' => 'trainer',
            'user_status' => 'active',
            'consecutive_expired_invitations' => 0,
        ]);

        $invitation = TrainerNotification::create([
            'trainer_id' => $trainer->id,
            'type' => 'course_invitation',
            'title' => 'Undangan Mengajar',
            'message' => 'Silakan respon dalam 24 jam.',
            'invitation_status' => 'pending',
            'data' => [
                'entity_type' => 'course',
                'entity_id' => 1001,
                'invitation_status' => 'pending',
                'due_at' => now()->subHour()->toIso8601String(),
            ],
        ]);

        $this->artisan('trainer:send-invitation-overdue-alerts')->assertSuccessful();

        $invitation->refresh();
        $trainer->refresh();

        $this->assertSame('expired', $invitation->invitation_status);
        $this->assertSame('expired', (string) data_get($invitation->data, 'invitation_status'));
        $this->assertNotEmpty(data_get($invitation->data, 'expired_at'));
        $this->assertSame(1, (int) $trainer->consecutive_expired_invitations);

        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $admin->id,
            'type' => 'trainer_invitation_overdue_alert',
        ]);
    }

    /** @test */
    public function trainer_becomes_inactive_after_three_consecutive_expired_invitations(): void
    {
        User::factory()->create(['role' => 'admin']);

        $trainer = User::factory()->create([
            'role' => 'trainer',
            'user_status' => 'active',
            'consecutive_expired_invitations' => 0,
        ]);

        for ($i = 1; $i <= 3; $i++) {
            TrainerNotification::create([
                'trainer_id' => $trainer->id,
                'type' => 'event_invitation',
                'title' => 'Undangan Event #' . $i,
                'message' => 'Silakan respon dalam 24 jam.',
                'invitation_status' => 'pending',
                'data' => [
                    'entity_type' => 'event',
                    'entity_id' => 2000 + $i,
                    'invitation_status' => 'pending',
                    'due_at' => now()->subHours(2)->toIso8601String(),
                ],
            ]);
        }

        $this->artisan('trainer:send-invitation-overdue-alerts')->assertSuccessful();

        $trainer->refresh();

        $this->assertSame(3, (int) $trainer->consecutive_expired_invitations);
        $this->assertSame('inactive', (string) $trainer->user_status);
    }

    /** @test */
    public function trainer_is_suspended_after_three_consecutive_late_uploads(): void
    {
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'user_status' => 'active',
            'consecutive_late_uploads' => 0,
            'late_uploads' => 0,
        ]);

        for ($i = 1; $i <= 3; $i++) {
            TrainerNotification::create([
                'trainer_id' => $trainer->id,
                'type' => 'course_invitation',
                'title' => 'Undangan Course #' . $i,
                'message' => 'Sudah diterima, upload dalam 3 hari.',
                'invitation_status' => 'accepted',
                'data' => [
                    'entity_type' => 'course',
                    'entity_id' => 3000 + $i,
                    'invitation_status' => 'accepted',
                    'upload_due_at' => now()->subDay()->toIso8601String(),
                ],
            ]);
        }

        $this->artisan('trainer:send-invitation-overdue-alerts')->assertSuccessful();

        $trainer->refresh();

        $this->assertSame(3, (int) $trainer->consecutive_late_uploads);
        $this->assertSame(3, (int) $trainer->late_uploads);
        $this->assertSame('suspended', (string) $trainer->user_status);
    }

    /** @test */
    public function instant_whitelist_resets_late_strike_to_zero(): void
    {
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'user_status' => 'active',
            'consecutive_late_uploads' => 2,
            'late_uploads' => 2,
        ]);

        /** @var TrainerActivityService $activityService */
        $activityService = app(TrainerActivityService::class);
        $activityService->resetLateUploads($trainer, [
            'entity_type' => 'course',
            'entity_id' => 4001,
            'entity_title' => 'Course Test',
        ]);

        $trainer->refresh();

        $this->assertSame(0, (int) $trainer->consecutive_late_uploads);
        $this->assertSame(0, (int) $trainer->late_uploads);
        $this->assertDatabaseHas('trainer_notifications', [
            'trainer_id' => $trainer->id,
            'type' => 'trainer_strike_reset',
        ]);
    }

    /** @test */
    public function admin_trainer_detail_and_activity_service_include_cospeaker_and_assigned_events_feedback(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $trainer = User::factory()->create(['role' => 'trainer']);

        // Create main event (where trainer is NOT main trainer, but co-speaker)
        $event1 = \App\Models\Event::create([
            'title' => 'Co-speaker Event',
            'image' => 'uploads/events/test.jpg',
            'speaker' => 'Guest Speaker',
            'description' => 'Test event description',
            'location' => 'Online',
            'price' => 0,
            'event_time' => '10:00:00',
            'event_date' => now()->subDay()->toDateString(),
            'material_status' => 'approved',
        ]);

        \App\Models\EventSpeaker::create([
            'event_id' => $event1->id,
            'trainer_id' => $trainer->id,
            'name' => $trainer->name,
            'salary' => 1000,
            'order' => 0,
        ]);

        // Create feedback for event1
        \App\Models\Feedback::create([
            'event_id' => $event1->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 5.0,
            'feedback' => 'Amazing session by co-speaker!',
        ]);

        // Create another event (where trainer is NOT main trainer, but has accepted trainer assignment)
        $event2 = \App\Models\Event::create([
            'title' => 'Assigned Event',
            'image' => 'uploads/events/test.jpg',
            'speaker' => 'Guest Speaker',
            'description' => 'Test event description',
            'location' => 'Online',
            'price' => 0,
            'event_time' => '10:00:00',
            'event_date' => now()->subDay()->toDateString(),
            'material_status' => 'approved',
        ]);

        \App\Models\TrainerAssignment::create([
            'trainer_id' => $trainer->id,
            'event_id' => $event2->id,
            'scheme_type' => 1,
            'status' => 'accepted',
            'sla_upload_deadline' => now()->addDays(3),
        ]);

        // Create feedback for event2
        \App\Models\Feedback::create([
            'event_id' => $event2->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 4.0,
            'feedback' => 'Great assignment execution!',
        ]);

        // Calculate via Service
        /** @var TrainerActivityService $activityService */
        $activityService = app(TrainerActivityService::class);
        $summary = $activityService->refresh($trainer);

        // Average rating should be (5 + 4) / 2 = 4.5
        $this->assertEquals(4.5, (float) $summary['average_rating']);

        // Check via Admin Controller
        $response = $this->actingAs($admin)->get(route('admin.trainer.show', $trainer));

        $response->assertOk();
        $response->assertViewHas('trainerEvents', function ($events) use ($event1, $event2) {
            return $events->contains('id', $event1->id) && $events->contains('id', $event2->id);
        });
        $response->assertViewHas('eventFeedback', function ($feedbacks) use ($event1, $event2) {
            return $feedbacks->count() === 2;
        });
        $response->assertViewHas('averageRating', 4.5);
    }
}

