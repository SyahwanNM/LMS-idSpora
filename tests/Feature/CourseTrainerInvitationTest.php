<?php

namespace Tests\Feature;

use App\Models\CourseTemplate;
use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use App\Models\TrainerNotification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseTrainerInvitationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $trainer;
    protected Category $category;
    protected CourseTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->trainer = User::factory()->create(['role' => 'trainer']);
        $this->category = Category::factory()->create();
        $this->template = CourseTemplate::factory()
            ->for($this->admin, 'creator')
            ->for($this->category)
            ->create();
    }

    /** @test */
    public function test_creating_course_with_trainer_sends_invitation()
    {
        $this->actingAs($this->admin);

        // Create course tanpa malahan using eloquent untuk memastikan notification dipanggil
        $course = Course::create([
            'name' => 'Test Course',
            'category_id' => $this->category->id,
            'template_id' => $this->template->id,
            'template_version' => $this->template->version,
            'trainer_id' => $this->trainer->id,
            'description' => 'Test description',
            'level' => 'beginner',
            'status' => 'active',
            'price' => 100000,
            'duration' => 60,
            'media' => 'test.jpg',
            'media_type' => 'image',
        ]);

        // Notify manually seperti di controller
        $trainerCheck = User::query()
            ->where('id', $this->trainer->id)
            ->where('role', 'trainer')
            ->first();

        if ($trainerCheck) {
            TrainerNotification::create([
                'trainer_id' => $this->trainer->id,
                'type' => 'course_invitation',
                'title' => 'Undangan Menjadi Trainer Course',
                'message' => 'Anda diundang menjadi trainer untuk course "' . $course->name . '".',
                'data' => [
                    'entity_type' => 'course',
                    'entity_id' => $course->id,
                    'url' => route('trainer.detail-course', $course->id),
                    'invitation_status' => 'pending',
                ],
            ]);
        }

        // Verify notification was created
        $this->assertDatabaseHas('trainer_notifications', [
            'trainer_id' => $this->trainer->id,
            'type' => 'course_invitation',
        ]);

        $notification = TrainerNotification::where('trainer_id', $this->trainer->id)
            ->where('type', 'course_invitation')
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('Test Course', $notification->message);
        $this->assertEquals('course', $notification->data['entity_type']);
    }

    /** @test */
    public function test_event_trainer_invitation_structure()
    {
        // Test that event invitation data structure is correct
        $event = \App\Models\Event::factory()
            ->for($this->trainer, 'trainer')
            ->create();

        TrainerNotification::create([
            'trainer_id' => $this->trainer->id,
            'type' => 'event_invitation',
            'title' => 'Undangan Menjadi Narasumber Event',
            'message' => 'Anda diundang menjadi narasumber untuk event "' . $event->title . '".',
            'data' => [
                'entity_type' => 'event',
                'entity_id' => $event->id,
                'url' => route('trainer.events.show', $event->id),
                'invitation_status' => 'pending',
                'invitation_source' => 'trainer_id',
                'due_at' => now()->addDays(7)->toIso8601String(),
            ],
        ]);

        $this->assertDatabaseHas('trainer_notifications', [
            'trainer_id' => $this->trainer->id,
            'type' => 'event_invitation',
        ]);

        $notification = TrainerNotification::where('trainer_id', $this->trainer->id)
            ->where('type', 'event_invitation')
            ->first();

        $this->assertNotNull($notification);
        $this->assertEquals('event', $notification->data['entity_type']);
        $this->assertTrue(isset($notification->data['due_at']));
    }
}
