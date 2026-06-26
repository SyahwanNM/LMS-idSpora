<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventTrainerModule;
use App\Models\TrainerAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventMaterialLogoStampingTest extends TestCase
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
            'title' => 'Test Event Stamping',
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
            'material_path' => 'uploads/materials/test_assignment.png',
            'material_status' => 'pending_review',
        ]);
    }

    /** @test */
    public function admin_can_approve_module_and_stamp_logo_on_image()
    {
        Storage::fake('public');

        // Create a dummy image file
        $dummyImage = imagecreatetruecolor(200, 200);
        ob_start();
        imagepng($dummyImage);
        $imageData = ob_get_clean();
        imagedestroy($dummyImage);

        // Put the dummy file in storage
        Storage::disk('public')->put('uploads/materials/test_image.png', $imageData);

        // Ensure logo exists in public_path('aset/logo idspora_dark.png')
        // In the test environment, we can mock public_path or make sure the directory and logo exist.
        // Let's copy the real logo or write a dummy logo if it doesn't exist during test.
        $logoDir = public_path('aset');
        if (!file_exists($logoDir)) {
            mkdir($logoDir, 0777, true);
        }
        $logoPath = public_path('aset/logo idspora_dark.png');
        if (!file_exists($logoPath)) {
            $dummyLogo = imagecreatetruecolor(50, 50);
            ob_start();
            imagepng($dummyLogo);
            $logoData = ob_get_clean();
            imagedestroy($dummyLogo);
            file_put_contents($logoPath, $logoData);
        }

        $module = EventTrainerModule::create([
            'event_id' => $this->event->id,
            'trainer_id' => $this->trainer->id,
            'original_name' => 'test_image.png',
            'path' => 'uploads/materials/test_image.png',
            'status' => 'pending_review',
        ]);

        $originalSize = strlen($imageData);

        // Call approve with stamp_logo => 1
        $response = $this->actingAs($this->admin)->post(route('admin.event-material.approve', $this->event->id), [
            'module_id' => $module->id,
            'stamp_logo' => '1',
        ]);

        $response->assertRedirect();

        $module->refresh();
        $this->assertEquals('approved', $module->status);
        $this->assertTrue($module->logo_stamped);

        // Verify the file was indeed stamped (exists and size is changed)
        $this->assertTrue(Storage::disk('public')->exists('uploads/materials/test_image.png'));
        $finalSize = Storage::disk('public')->size('uploads/materials/test_image.png');
        $this->assertNotEquals($originalSize, $finalSize);
    }

    /** @test */
    public function admin_can_approve_module_and_stamp_logo_on_pdf()
    {
        Storage::fake('public');

        $logoDir = public_path('aset');
        if (!file_exists($logoDir)) {
            mkdir($logoDir, 0777, true);
        }
        $logoPath = public_path('aset/logo idspora_dark.png');
        if (!file_exists($logoPath)) {
            $dummyLogo = imagecreatetruecolor(50, 50);
            ob_start();
            imagepng($dummyLogo);
            $logoData = ob_get_clean();
            imagedestroy($dummyLogo);
            file_put_contents($logoPath, $logoData);
        }

        $pdfPath = Storage::disk('public')->path('uploads/materials/test_doc.pdf');
        $pdfDir = dirname($pdfPath);
        if (!file_exists($pdfDir)) {
            mkdir($pdfDir, 0777, true);
        }

        $generateCmd = sprintf(
            'python -c "from reportlab.pdfgen import canvas; c = canvas.Canvas(r\'%s\'); c.drawString(100, 100, \'Test Document\'); c.save()"',
            $pdfPath
        );
        exec($generateCmd);

        $this->assertTrue(file_exists($pdfPath));
        $originalSize = filesize($pdfPath);

        $module = EventTrainerModule::create([
            'event_id' => $this->event->id,
            'trainer_id' => $this->trainer->id,
            'original_name' => 'test_doc.pdf',
            'path' => 'uploads/materials/test_doc.pdf',
            'status' => 'pending_review',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.event-material.approve', $this->event->id), [
            'module_id' => $module->id,
            'stamp_logo' => '1',
        ]);

        $response->assertRedirect();

        $module->refresh();
        $this->assertEquals('approved', $module->status);
        $this->assertTrue($module->logo_stamped);

        // Verify the file was indeed stamped and size changed
        $this->assertTrue(Storage::disk('public')->exists('uploads/materials/test_doc.pdf'));
        $finalSize = Storage::disk('public')->size('uploads/materials/test_doc.pdf');
        $this->assertNotEquals($originalSize, $finalSize);
    }

    /** @test */
    public function admin_can_revoke_approval_and_restore_original_unstamped_file()
    {
        Storage::fake('public');

        $logoDir = public_path('aset');
        if (!file_exists($logoDir)) {
            mkdir($logoDir, 0777, true);
        }
        $logoPath = public_path('aset/logo idspora_dark.png');
        if (!file_exists($logoPath)) {
            $dummyLogo = imagecreatetruecolor(50, 50);
            ob_start();
            imagepng($dummyLogo);
            $logoData = ob_get_clean();
            imagedestroy($dummyLogo);
            file_put_contents($logoPath, $logoData);
        }

        // Create a dummy image
        $dummyImage = imagecreatetruecolor(200, 200);
        ob_start();
        imagepng($dummyImage);
        $imageData = ob_get_clean();
        imagedestroy($dummyImage);

        Storage::disk('public')->put('uploads/materials/test_restore.png', $imageData);
        $originalSize = strlen($imageData);

        $module = EventTrainerModule::create([
            'event_id' => $this->event->id,
            'trainer_id' => $this->trainer->id,
            'original_name' => 'test_restore.png',
            'path' => 'uploads/materials/test_restore.png',
            'status' => 'pending_review',
        ]);

        // 1. Approve and stamp logo
        $response = $this->actingAs($this->admin)->post(route('admin.event-material.approve', $this->event->id), [
            'module_id' => $module->id,
            'stamp_logo' => '1',
        ]);
        $response->assertRedirect();

        $module->refresh();
        $this->assertEquals('approved', $module->status);
        $this->assertTrue($module->logo_stamped);

        // Stamped file size should change
        $stampedSize = Storage::disk('public')->size('uploads/materials/test_restore.png');
        $this->assertNotEquals($originalSize, $stampedSize);

        // Backup file (.original) should exist
        $this->assertTrue(file_exists(Storage::disk('public')->path('uploads/materials/test_restore.png.original')));

        // 2. Revoke approval
        $response2 = $this->actingAs($this->admin)->post(route('admin.event-material.revoke', $this->event->id), [
            'module_id' => $module->id,
        ]);
        $response2->assertRedirect();

        $module->refresh();
        $this->assertEquals('pending_review', $module->status);
        $this->assertFalse($module->logo_stamped);

        // Restored file size should match original size
        $restoredSize = Storage::disk('public')->size('uploads/materials/test_restore.png');
        $this->assertEquals($originalSize, $restoredSize);

        // Backup file (.original) should no longer exist
        $this->assertFalse(file_exists(Storage::disk('public')->path('uploads/materials/test_restore.png.original')));
    }
}
