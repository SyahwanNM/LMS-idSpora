<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventSpeaker;
use App\Models\TrainerAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainerEventsVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private User $trainer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trainer = User::factory()->create([
            'role' => 'trainer',
        ]);
    }

    private function createBaseEvent(array $overrides = []): Event
    {
        return Event::create(array_merge([
            'title' => 'Trainer Visibility Test Event',
            'image' => 'uploads/events/test.jpg',
            'speaker' => 'Guest Speaker',
            'description' => 'Test event description',
            'location' => 'Online',
            'price' => 0,
            'event_time' => '10:00:00',
            'event_date' => now()->addDay()->toDateString(),
        ], $overrides));
    }

    public function test_dashboard_counts_event_from_speaker_relation_without_trainer_id(): void
    {
        $event = $this->createBaseEvent(['trainer_id' => null]);

        EventSpeaker::create([
            'event_id' => $event->id,
            'trainer_id' => $this->trainer->id,
            'name' => $this->trainer->name,
            'salary' => 0,
            'order' => 0,
        ]);

        $response = $this->actingAs($this->trainer)->get(route('trainer.dashboard'));

        $response->assertOk();
        $response->assertViewHas('activeEventCount', 1);
        $response->assertViewHas('priorityEvents', function ($events) use ($event) {
            return $events->contains('id', $event->id);
        });
    }

    public function test_events_page_lists_event_from_accepted_assignment_without_trainer_id(): void
    {
        $event = $this->createBaseEvent(['trainer_id' => null]);

        TrainerAssignment::create([
            'trainer_id' => $this->trainer->id,
            'event_id' => $event->id,
            'scheme_type' => 1,
            'status' => 'accepted',
            'sla_upload_deadline' => now()->addDays(3),
        ]);

        $response = $this->actingAs($this->trainer)->get(route('trainer.events'));

        $response->assertOk();
        $response->assertViewHas('events', function ($events) use ($event) {
            return $events->contains('id', $event->id);
        });
    }

    public function test_event_module_completeness_rules(): void
    {
        // 1. Setup Event with 2 assigned trainers (main trainer + 1 co-speaker trainer)
        $trainer2 = User::factory()->create(['role' => 'trainer']);
        $event = $this->createBaseEvent([
            'trainer_id' => $this->trainer->id,
        ]);

        EventSpeaker::create([
            'event_id' => $event->id,
            'trainer_id' => $trainer2->id,
            'name' => $trainer2->name,
            'salary' => 0,
            'order' => 0,
        ]);

        // Initially, no modules approved
        $this->assertFalse($event->has_approved_modules);

        // Approve module for trainer 1
        \App\Models\EventTrainerModule::create([
            'event_id' => $event->id,
            'trainer_id' => $this->trainer->id,
            'name' => 'Module 1',
            'original_name' => 'module1.pdf',
            'path' => 'events/modules/module1.pdf',
            'status' => 'approved',
        ]);

        // Still false, because trainer 2 does not have an approved module
        $this->assertFalse($event->has_approved_modules);

        // Approve module for trainer 2
        \App\Models\EventTrainerModule::create([
            'event_id' => $event->id,
            'trainer_id' => $trainer2->id,
            'name' => 'Module 2',
            'original_name' => 'module2.pdf',
            'path' => 'events/modules/module2.pdf',
            'status' => 'approved',
        ]);

        // Now both have approved modules, should be true
        $this->assertTrue($event->has_approved_modules);

        // 2. Setup Event with 0 assigned trainers (trainer_id null and no speakers with trainer_id)
        $eventNoTrainer = $this->createBaseEvent([
            'trainer_id' => null,
        ]);
        // Should automatically be true since no trainers are assigned to complete modules
        $this->assertTrue($eventNoTrainer->has_approved_modules);
    }

    public function test_event_unassign_trainer_clears_database_and_makes_it_complete(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = $this->createBaseEvent([
            'trainer_id' => $this->trainer->id,
            'speaker' => $this->trainer->name,
        ]);

        EventSpeaker::create([
            'event_id' => $event->id,
            'trainer_id' => $this->trainer->id,
            'name' => $this->trainer->name,
            'salary' => 1000,
            'order' => 0,
        ]);

        // Initially, the trainer is assigned and no modules are approved, so completeness is false
        $this->assertFalse($event->has_approved_modules);

        // Perform edit/update via controller, but with a guest speaker (no registered trainer)
        $response = $this->actingAs($admin)->put(route('admin.events.update', $event), [
            'title' => 'Updated Event Title',
            'speaker' => 'Guest Speaker Only',
            'speakers' => ['Guest Speaker Only'],
            'speaker_salaries' => [0],
            'manage_action' => 'manage',
            'short_description' => 'This is a short description of the event.',
            'description' => 'This is a long description.',
            'location' => 'Online',
            'location_mode' => 'online',
            'price' => 0,
            'event_date' => now()->addDay()->toDateString(),
            'event_time' => '10:00',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $event->refresh();

        // The trainer_id and speakers with trainer_id should be cleared
        $this->assertNull($event->trainer_id);
        $this->assertEquals(0, $event->speakers()->whereNotNull('trainer_id')->count());

        // Therefore, it should now automatically be complete
        $this->assertTrue($event->has_approved_modules);
    }

    public function test_event_invitation_auto_created_on_duplicate(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // 1. Create a base event assigned to the trainer
        $event = $this->createBaseEvent([
            'trainer_id' => $this->trainer->id,
            'speaker' => $this->trainer->name,
        ]);

        // Manually create the invitation for the original event (like the creation flow does)
        \App\Models\TrainerNotification::create([
            'trainer_id' => $this->trainer->id,
            'type' => 'event_invitation',
            'title' => 'Original Invitation',
            'message' => 'Original',
            'invitation_status' => 'pending',
            'data' => [
                'entity_type' => 'event',
                'entity_id' => $event->id,
                'url' => route('trainer.events.show', $event->id),
            ],
        ]);

        // 2. Perform the duplicate event request as admin
        $response = $this->actingAs($admin)->post(route('admin.events.duplicate', $event));
        $response->assertStatus(201);
        
        $duplicatedEventId = $response->json('data.id');
        $this->assertNotNull($duplicatedEventId);
        $this->assertNotEquals($event->id, $duplicatedEventId);

        // Verify that the duplicated event has the trainer_id and speaker name duplicated
        $duplicatedEvent = Event::find($duplicatedEventId);
        $this->assertEquals($this->trainer->id, $duplicatedEvent->trainer_id);
        $this->assertEquals($this->trainer->name, $duplicatedEvent->speaker);

        // Verify that no TrainerNotification of type event_invitation exists yet for the duplicated event
        $originalNotificationCount = \App\Models\TrainerNotification::where('trainer_id', $this->trainer->id)
            ->where('type', 'event_invitation')
            ->count();
        $this->assertEquals(1, $originalNotificationCount); // only the original one exists

        // 3. Act as the trainer and access the dashboard or get notifications index
        $this->actingAs($this->trainer);
        
        // Visit notifications endpoint or dashboard
        $notifResponse = $this->get(route('trainer.notifications.index'));
        $notifResponse->assertOk();

        // 4. Assert that the notification has been dynamically created for the duplicated event!
        $newNotifications = \App\Models\TrainerNotification::where('trainer_id', $this->trainer->id)
            ->where('type', 'event_invitation')
            ->get();
        
        $this->assertCount(2, $newNotifications);

        // Find the one for the duplicated event
        $duplicatedNotification = $newNotifications->first(function ($notif) use ($duplicatedEventId) {
            return (int) data_get($notif->data, 'entity_id') === (int) $duplicatedEventId;
        });

        $this->assertNotNull($duplicatedNotification);
        $this->assertEquals('pending', $duplicatedNotification->invitation_status);
        $this->assertEquals('pending', data_get($duplicatedNotification->data, 'invitation_status'));
        $this->assertEquals('event', data_get($duplicatedNotification->data, 'entity_type'));
    }

    public function test_notifications_index_renders_html_for_browser_and_json_for_ajax(): void
    {
        $this->actingAs($this->trainer);

        // 1. Send normal GET request (browser request)
        $htmlResponse = $this->get(route('trainer.notifications.index'));
        $htmlResponse->assertOk();
        $htmlResponse->assertSee('Daftar Undangan');
        $htmlResponse->assertSee('Kembali ke Dashboard');

        // 2. Send AJAX/JSON GET request
        $jsonResponse = $this->getJson(route('trainer.notifications.index'));
        $jsonResponse->assertOk();
        $jsonResponse->assertJsonStructure([
            'items',
            'unread',
        ]);
    }

    public function test_accept_event_invitation_with_scheme(): void
    {
        $this->actingAs($this->trainer);

        $event = $this->createBaseEvent([
            'trainer_id' => $this->trainer->id,
            'speaker' => $this->trainer->name,
        ]);

        $notification = \App\Models\TrainerNotification::create([
            'trainer_id' => $this->trainer->id,
            'type' => 'event_invitation',
            'title' => 'Event Invitation',
            'message' => 'Please join',
            'invitation_status' => 'pending',
            'data' => [
                'entity_type' => 'event',
                'entity_id' => $event->id,
                'url' => route('trainer.events.show', $event->id),
            ],
        ]);

        $response = $this->post(route('trainer.notifications.accept-with-scheme', $notification), [
            'scheme_type' => 2,
            'legal_agreement_1' => '1',
            'legal_agreement_2' => '1',
        ]);

        $response->assertRedirect(route('trainer.events.show', $event->id));
        $response->assertSessionHas('success');

        $notification->refresh();
        $this->assertEquals('accepted', $notification->invitation_status);
        $this->assertEquals(2, data_get($notification->data, 'scheme_type'));

        $assignment = TrainerAssignment::where('trainer_id', $this->trainer->id)
            ->where('event_id', $event->id)
            ->first();
        
        $this->assertNotNull($assignment);
        $this->assertEquals('accepted', $assignment->status);
        $this->assertEquals(2, $assignment->scheme_type);
    }

    public function test_trainer_finance_page_renders_with_correct_data(): void
    {
        $this->actingAs($this->trainer);

        // Create a trainer payment
        \App\Models\TrainerPayment::create([
            'user_id' => $this->trainer->id,
            'type' => 'course_payout',
            'trainer_name' => $this->trainer->name,
            'title' => 'Pencairan Test',
            'amount' => 500000,
            'status' => 'approved',
            'payment_date' => now(),
        ]);

        $response = $this->get(route('trainer.finance'));
        $response->assertOk();
        $response->assertSee('Total Pendapatan Aktual');
        $response->assertSee('Estimasi Pendapatan');
        $response->assertSee('Rp 500.000');
    }

    public function test_event_duplication_cleans_up_orphans(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // 1. Create a template event
        $originalEvent = $this->createBaseEvent();

        $nextId = $originalEvent->id + 1;

        // Create orphan records in DB manually for event_id = 2
        // We use user_saved_events pivot table because it does not have a foreign key constraint,
        // allowing us to insert an orphan record without violating SQLite FK constraints.
        \Illuminate\Support\Facades\DB::table('user_saved_events')->insert([
            'user_id' => $this->trainer->id,
            'event_id' => $nextId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Call duplicate endpoint
        $response = $this->post(route('admin.events.duplicate', $originalEvent->id));
        $response->assertStatus(201);

        $duplicatedEventId = $response->json('data.id');
        $this->assertEquals($nextId, $duplicatedEventId);

        // 4. Verify that the orphan records for the new event ID were cleaned up
        $savedEventsCount = \Illuminate\Support\Facades\DB::table('user_saved_events')->where('event_id', $duplicatedEventId)->count();
        $this->assertEquals(0, $savedEventsCount, 'Orphan user_saved_events should be cleared during duplication');
    }
}
