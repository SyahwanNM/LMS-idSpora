<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventTrainerModule;
use App\Models\TrainerAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventMaterialRevocationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $trainer;
    private Event $event;
    private TrainerAssignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->trainer = User::factory()->create(['role' => 'trainer']);

        $this->event = Event::create([
            'title' => 'Test Event Revocation',
            'image' => 'uploads/events/test.jpg',
            'speaker' => 'Speaker Test',
            'description' => 'Test event description',
            'location' => 'Online',
            'price' => 0,
            'event_time' => '10:00:00',
            'event_date' => now()->addDays(5)->toDateString(),
            'trainer_id' => $this->trainer->id,
            'material_status' => 'pending_review',
        ]);

        $this->assignment = TrainerAssignment::create([
            'trainer_id' => $this->trainer->id,
            'event_id' => $this->event->id,
            'scheme_type' => 1,
            'status' => 'accepted',
            'material_path' => 'uploads/materials/test.pdf',
            'material_status' => 'pending_review',
        ]);
    }

    /** @test */
    public function admin_can_approve_individual_module_and_it_syncs()
    {
        $module = EventTrainerModule::create([
            'event_id' => $this->event->id,
            'trainer_id' => $this->trainer->id,
            'original_name' => 'Module 1.pdf',
            'path' => 'uploads/materials/m1.pdf',
            'status' => 'pending_review',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.event-material.approve', $this->event->id), [
            'module_id' => $module->id,
        ]);

        $response->assertRedirect();
        
        $module->refresh();
        $this->assignment->refresh();
        $this->event->refresh();

        $this->assertEquals('approved', $module->status);
        $this->assertEquals('approved', $this->assignment->material_status);
        $this->assertEquals('approved', $this->event->material_status);
    }

    /** @test */
    public function admin_can_reject_individual_module_and_it_syncs()
    {
        $module = EventTrainerModule::create([
            'event_id' => $this->event->id,
            'trainer_id' => $this->trainer->id,
            'original_name' => 'Module 1.pdf',
            'path' => 'uploads/materials/m1.pdf',
            'status' => 'pending_review',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.event-material.reject', $this->event->id), [
            'module_id' => $module->id,
            'rejection_reason' => 'Need more detail on slide 5',
        ]);

        $response->assertRedirect();

        $module->refresh();
        $this->assignment->refresh();
        $this->event->refresh();

        $this->assertEquals('rejected', $module->status);
        $this->assertEquals('rejected', $this->assignment->material_status);
        $this->assertEquals('rejected', $this->event->material_status);
        $this->assertEquals('Need more detail on slide 5', $module->rejection_reason);
        $this->assertEquals('Need more detail on slide 5', $this->assignment->material_rejection_reason);
        $this->assertEquals('Need more detail on slide 5', $this->event->material_rejection_reason);
    }

    /** @test */
    public function admin_can_revoke_module_approval_back_to_pending_review()
    {
        $module = EventTrainerModule::create([
            'event_id' => $this->event->id,
            'trainer_id' => $this->trainer->id,
            'original_name' => 'Module 1.pdf',
            'path' => 'uploads/materials/m1.pdf',
            'status' => 'approved',
            'reviewed_by' => $this->admin->id,
            'reviewed_at' => now(),
        ]);

        $this->assignment->update([
            'material_status' => 'approved',
            'material_approved_by' => $this->admin->id,
            'material_approved_at' => now(),
        ]);

        $this->event->update([
            'material_status' => 'approved',
            'material_approved_by' => $this->admin->id,
            'material_approved_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.event-material.revoke', $this->event->id), [
            'module_id' => $module->id,
        ]);

        $response->assertRedirect();

        $module->refresh();
        $this->assignment->refresh();
        $this->event->refresh();

        $this->assertEquals('pending_review', $module->status);
        $this->assertNull($module->reviewed_by);
        $this->assertNull($module->reviewed_at);

        $this->assertEquals('pending_review', $this->assignment->material_status);
        $this->assertNull($this->assignment->material_approved_by);
        $this->assertNull($this->assignment->material_approved_at);

        $this->assertEquals('pending_review', $this->event->material_status);
        $this->assertNull($this->event->material_approved_by);
        $this->assertNull($this->event->material_approved_at);
    }

    /** @test */
    public function admin_can_revoke_assignment_approval_back_to_pending_review()
    {
        $module = EventTrainerModule::create([
            'event_id' => $this->event->id,
            'trainer_id' => $this->trainer->id,
            'original_name' => 'Module 1.pdf',
            'path' => 'uploads/materials/m1.pdf',
            'status' => 'approved',
        ]);

        $this->assignment->update([
            'material_status' => 'approved',
            'material_approved_by' => $this->admin->id,
            'material_approved_at' => now(),
        ]);

        $this->event->update([
            'material_status' => 'approved',
            'material_approved_by' => $this->admin->id,
            'material_approved_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.event-material.revoke', $this->event->id), [
            'assignment_id' => $this->assignment->id,
        ]);

        $response->assertRedirect();

        $module->refresh();
        $this->assignment->refresh();
        $this->event->refresh();

        $this->assertEquals('pending_review', $module->status);
        $this->assertEquals('pending_review', $this->assignment->material_status);
        $this->assertEquals('pending_review', $this->event->material_status);
    }

    /** @test */
    public function trainer_can_delete_submitted_module_if_status_allows_and_it_syncs()
    {
        $module = EventTrainerModule::create([
            'event_id' => $this->event->id,
            'trainer_id' => $this->trainer->id,
            'original_name' => 'Module 1.pdf',
            'path' => 'uploads/materials/m1.pdf',
            'status' => 'approved',
        ]);

        $this->assignment->update([
            'material_status' => 'approved',
            'material_path' => 'uploads/materials/m1.pdf',
        ]);

        $this->event->update([
            'material_status' => 'approved',
            'module_path' => 'uploads/materials/m1.pdf',
        ]);

        $response = $this->actingAs($this->trainer)->post(route('trainer.events.studio.upload', $this->event->id), [
            'action' => 'delete_module',
            'module_id' => $module->id,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('event_trainer_modules', [
            'id' => $module->id,
        ]);

        $this->assignment->refresh();
        $this->event->refresh();

        $this->assertEquals('pending', $this->assignment->material_status);
        $this->assertNull($this->assignment->material_path);
        $this->assertEquals('pending', $this->event->material_status);
        $this->assertNull($this->event->module_path);
    }

    /** @test */
    public function trainer_can_delete_submitted_module_during_pending_review()
    {
        $module = EventTrainerModule::create([
            'event_id' => $this->event->id,
            'trainer_id' => $this->trainer->id,
            'original_name' => 'Module 1.pdf',
            'path' => 'uploads/materials/m1.pdf',
            'status' => 'pending_review',
        ]);

        $this->assignment->update([
            'material_status' => 'pending_review',
            'material_path' => 'uploads/materials/m1.pdf',
        ]);

        $this->event->update([
            'material_status' => 'pending_review',
            'module_path' => 'uploads/materials/m1.pdf',
        ]);

        $response = $this->actingAs($this->trainer)->post(route('trainer.events.studio.upload', $this->event->id), [
            'action' => 'delete_module',
            'module_id' => $module->id,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('event_trainer_modules', [
            'id' => $module->id,
        ]);

        $this->assignment->refresh();
        $this->event->refresh();

        $this->assertEquals('pending', $this->assignment->material_status);
        $this->assertNull($this->assignment->material_path);
        $this->assertEquals('pending', $this->event->material_status);
        $this->assertNull($this->event->module_path);
    }
}
