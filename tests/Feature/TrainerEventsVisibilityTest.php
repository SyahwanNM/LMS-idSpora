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
}
