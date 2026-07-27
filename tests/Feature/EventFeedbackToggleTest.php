<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventFeedbackToggleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_toggle_event_feedback_button_visibility()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin_test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $event = Event::create([
            'title' => 'Test Event',
            'speaker' => 'Test Speaker',
            'materi' => 'Test Material',
            'jenis' => 'Webinar',
            'location' => 'Online',
            'price' => 0,
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'event_time' => '09:00',
            'description' => 'Test Description',
            'show_feedback' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.events.toggle-feedback', $event->id), [
            'show_feedback' => 0,
        ]);

        $response->assertRedirect();
        $event->refresh();
        $this->assertFalse((bool)$event->show_feedback);

        $response2 = $this->actingAs($admin)->post(route('admin.events.toggle-feedback', $event->id), [
            'show_feedback' => 1,
        ]);

        $response2->assertRedirect();
        $event->refresh();
        $this->assertTrue((bool)$event->show_feedback);
    }

    /** @test */
    public function admin_can_toggle_individual_module_feedback_button_visibility()
    {
        $admin = User::create([
            'name' => 'Admin Test 2',
            'email' => 'admin_test2@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $event = Event::create([
            'title' => 'Test Event 2',
            'speaker' => 'Test Speaker',
            'materi' => 'Test Material',
            'jenis' => 'Webinar',
            'location' => 'Online',
            'price' => 0,
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'event_time' => '09:00',
            'description' => 'Test Description',
            'show_feedback' => true,
        ]);

        $module = \App\Models\EventTrainerModule::create([
            'event_id' => $event->id,
            'trainer_id' => $admin->id,
            'original_name' => 'Module 1.pdf',
            'path' => 'uploads/materials/m1.pdf',
            'survey_link' => 'https://example.com/survey',
            'status' => 'approved',
            'show_feedback' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.events.modules.toggle-feedback', [$event->id, $module->id]), [
            'show_feedback' => 0,
        ]);

        $response->assertRedirect();
        $module->refresh();
        $this->assertFalse((bool) $module->show_feedback);

        $response2 = $this->actingAs($admin)->post(route('admin.events.modules.toggle-feedback', [$event->id, $module->id]), [
            'show_feedback' => 1,
        ]);

        $response2->assertRedirect();
        $module->refresh();
        $this->assertTrue((bool) $module->show_feedback);
    }
}
