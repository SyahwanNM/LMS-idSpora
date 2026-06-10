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
}
