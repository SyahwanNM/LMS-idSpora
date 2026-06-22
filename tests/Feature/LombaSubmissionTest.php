<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LombaSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
    }

    public function test_admin_can_create_lomba_without_speaker_and_with_valid_dates(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.events.store'), [
            'title' => 'Lomba Karya Tulis',
            'jenis' => 'Lomba',
            'manage_action' => 'manage',
            'short_description' => 'Short description',
            'description' => 'Long description',
            'location' => 'Online',
            'location_mode' => 'online',
            'price' => 0,
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '10:00',
            'start_submission' => now()->addDay()->format('Y-m-d\TH:i'),
            'until_submission' => now()->addDays(3)->format('Y-m-d\TH:i'),
            'announcement_date' => now()->addDays(5)->format('Y-m-d\TH:i'),
            'until_submission_2' => now()->addDays(8)->format('Y-m-d\TH:i'),
            'image' => UploadedFile::fake()->image('event.jpg'),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $event = Event::where('title', 'Lomba Karya Tulis')->first();
        $this->assertNotNull($event);
        $this->assertEquals('Lomba', $event->jenis);
        $this->assertEquals('', $event->speaker);
        $this->assertNotNull($event->start_submission);
        $this->assertNotNull($event->until_submission);
        $this->assertNotNull($event->announcement_date);
        $this->assertNotNull($event->until_submission_2);
    }

    public function test_admin_cannot_create_lomba_with_invalid_date_order(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.events.store'), [
            'title' => 'Lomba Invalid Dates',
            'jenis' => 'Lomba',
            'manage_action' => 'manage',
            'short_description' => 'Short description',
            'description' => 'Long description',
            'location' => 'Online',
            'location_mode' => 'online',
            'price' => 0,
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '10:00',
            'start_submission' => now()->addDays(3)->format('Y-m-d\TH:i'),
            'until_submission' => now()->addDays(2)->format('Y-m-d\TH:i'), // Invalid: until_submission < start_submission
            'announcement_date' => now()->addDays(5)->format('Y-m-d\TH:i'),
            'until_submission_2' => now()->addDays(8)->format('Y-m-d\TH:i'),
            'image' => UploadedFile::fake()->image('event.jpg'),
        ]);

        $response->assertSessionHasErrors(['until_submission']);
    }

    public function test_user_cannot_register_or_pay_for_lomba_after_until_submission_deadline(): void
    {
        // Create lomba where deadline has passed
        $event = Event::create([
            'title' => 'Lomba Kedaluwarsa',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDay(), // deadline is in the past
            'announcement_date' => now()->addDay(),
            'until_submission_2' => now()->addDays(3),
            'is_published' => true,
        ]);

        // Try registration endpoint (AJAX)
        $response = $this->actingAs($this->user)->post(route('events.register', $event));
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Pendaftaran Lomba sudah ditutup.',
        ]);

        // Try registration form endpoint
        $formResponse = $this->actingAs($this->user)->post(route('events.register.form', $event));
        $formResponse->assertRedirect();
        $formResponse->assertSessionHas('error', 'Pendaftaran Lomba sudah ditutup.');

        // Try checkout/payment page access
        $paymentResponse = $this->actingAs($this->user)->get(route('payment', $event));
        $paymentResponse->assertRedirect(route('events.show', $event));
        $paymentResponse->assertSessionHas('error', 'Pendaftaran Lomba sudah ditutup.');
    }

    public function test_user_can_upload_initial_submission_within_period(): void
    {
        $event = Event::create([
            'title' => 'Lomba Active',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDay(),
            'until_submission' => now()->addDay(),
            'announcement_date' => now()->addDays(2),
            'until_submission_2' => now()->addDays(4),
            'is_published' => true,
        ]);

        // Register the user
        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $file = UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('events.submit.initial', $event), [
            'submission_file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Initial submission successfully uploaded.');

        $registration->refresh();
        $this->assertNotNull($registration->submission_path);
        $this->assertEquals('pending', $registration->submission_status);
        $this->assertNotNull($registration->submission_uploaded_at);

        Storage::disk('public')->assertExists($registration->submission_path);
    }

    public function test_user_cannot_upload_initial_submission_after_deadline(): void
    {
        $event = Event::create([
            'title' => 'Lomba Submission Closed',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDay(), // closed
            'announcement_date' => now()->addDay(),
            'until_submission_2' => now()->addDays(3),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $file = UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('events.submit.initial', $event), [
            'submission_file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Pengiriman submission sudah ditutup.');
    }

    public function test_user_cannot_upload_initial_submission_if_status_is_tidak_lolos(): void
    {
        $event = Event::create([
            'title' => 'Lomba Status Tidak Lolos',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDay(),
            'until_submission' => now()->addDay(),
            'announcement_date' => now()->addDays(2),
            'until_submission_2' => now()->addDays(4),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_status' => 'tidak_lolos',
        ]);

        $file = UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('events.submit.initial', $event), [
            'submission_file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak dapat memperbarui submission karena dinyatakan tidak lolos.');
    }

    public function test_admin_can_review_initial_submission(): void
    {
        $event = Event::create([
            'title' => 'Lomba Review',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDay(),
            'until_submission' => now()->addDay(),
            'announcement_date' => now()->addDays(2),
            'until_submission_2' => now()->addDays(4),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now(),
            'submission_status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.events.submissions.review', [$event, $registration]), [
            'status' => 'lolos',
            'submission_notes' => 'Kerjaan sudah bagus, silakan lanjut ke tahap 2.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Status submission berhasil diperbarui.');

        $registration->refresh();
        $this->assertEquals('lolos', $registration->submission_status);
        $this->assertEquals('Kerjaan sudah bagus, silakan lanjut ke tahap 2.', $registration->submission_notes);
    }

    public function test_admin_can_reset_reviewed_submission_to_pending(): void
    {
        $event = Event::create([
            'title' => 'Lomba Reset Review',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDay(),
            'until_submission' => now()->addDay(),
            'announcement_date' => now()->addDays(2),
            'until_submission_2' => now()->addDays(4),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now(),
            'submission_status' => 'tidak_lolos',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.events.submissions.review', [$event, $registration]), [
            'status' => 'pending',
            'submission_notes' => 'Reset to pending.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Status submission berhasil diperbarui.');

        $registration->refresh();
        $this->assertEquals('pending', $registration->submission_status);
        $this->assertEquals('Reset to pending.', $registration->submission_notes);
        $this->assertEquals('not_required', $registration->stage2_payment_status);
    }

    public function test_lolos_user_can_upload_second_submission_after_announcement(): void
    {
        $event = Event::create([
            'title' => 'Lomba Stage 2',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDays(3),
            'announcement_date' => now()->subDay(), // announcement in past
            'until_submission_2' => now()->addDay(), // stage 2 open
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now()->subDays(4),
            'submission_status' => 'lolos', // user passed Stage 1 review
        ]);

        $file2 = UploadedFile::fake()->create('final_presentation.pdf', 1500, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('events.submit.second', $event), [
            'submission_file_2' => $file2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Second submission successfully uploaded.');

        $registration->refresh();
        $this->assertNotNull($registration->submission_path_2);
        $this->assertNotNull($registration->submission_2_uploaded_at);

        Storage::disk('public')->assertExists($registration->submission_path_2);
    }

    public function test_lolos_user_cannot_upload_second_submission_after_deadline2(): void
    {
        $event = Event::create([
            'title' => 'Lomba Stage 2 Closed',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDays(4),
            'announcement_date' => now()->subDays(3),
            'until_submission_2' => now()->subDay(), // deadline passed
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now()->subDays(5),
            'submission_status' => 'lolos',
        ]);

        $file2 = UploadedFile::fake()->create('final_presentation.pdf', 1500, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('events.submit.second', $event), [
            'submission_file_2' => $file2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Pengiriman submission kedua sudah ditutup.');
    }

    public function test_lomba_does_not_generate_qr_code_records(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.events.store'), [
            'title' => 'Lomba Tanpa QR',
            'jenis' => 'Lomba',
            'manage_action' => 'manage',
            'short_description' => 'Short description',
            'description' => 'Long description',
            'location' => 'Online',
            'location_mode' => 'online',
            'price' => 0,
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '10:00',
            'start_submission' => now()->addDay()->format('Y-m-d\TH:i'),
            'until_submission' => now()->addDays(3)->format('Y-m-d\TH:i'),
            'announcement_date' => now()->addDays(5)->format('Y-m-d\TH:i'),
            'until_submission_2' => now()->addDays(8)->format('Y-m-d\TH:i'),
            'image' => UploadedFile::fake()->image('event.jpg'),
        ]);

        $response->assertSessionHasNoErrors();
        $event = Event::where('title', 'Lomba Tanpa QR')->first();
        $this->assertNotNull($event);

        // Verify no attendance QR generated on event
        $this->assertNull($event->attendance_qr_token);
        $this->assertNull($event->attendance_qr_image);

        // Verify no EventDailyQr records created
        $dailyQrCount = \App\Models\EventDailyQr::where('event_id', $event->id)->count();
        $this->assertEquals(0, $dailyQrCount);
    }

    public function test_lomba_scan_requests_are_blocked(): void
    {
        $event = Event::create([
            'title' => 'Lomba Scan Blocked',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDay(),
            'until_submission' => now()->addDay(),
            'announcement_date' => now()->addDays(2),
            'until_submission_2' => now()->addDays(4),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        // GET request to scan page should redirect back with error
        $responseGet = $this->actingAs($this->user)->get(route('events.scan', $event));
        $responseGet->assertRedirect(route('events.registered.detail', $event));
        $responseGet->assertSessionHas('error', 'Lomba tidak memiliki QR Attendance.');

        // POST request to scan endpoint should return 403 json error
        $responsePost = $this->actingAs($this->user)->post(route('events.attendance.scan', $event), [
            'qr_text' => 'http://example.com/events/' . $event->id . '?t=sometoken',
        ]);
        $responsePost->assertStatus(403);
        $responsePost->assertJson(['message' => 'Lomba tidak memiliki QR Attendance.']);
    }

    public function test_admin_can_publish_lomba_if_completion_is_satisfied(): void
    {
        // 1. Offline Lomba (requires no VBG, no module, no attendance). Should publish instantly.
        $lombaOffline = Event::create([
            'title' => 'Lomba Offline Publishable',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'location' => 'Stadion Spora',
            'maps_url' => 'https://maps.google.com/xyz', // offline-only
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDay(),
            'until_submission' => now()->addDay(),
            'announcement_date' => now()->addDays(2),
            'until_submission_2' => now()->addDays(4),
            'is_published' => false,
        ]);

        $response1 = $this->actingAs($this->admin)->post(route('admin.events.publish', $lombaOffline));
        $response1->assertRedirect();
        $response1->assertSessionHas('success', 'Event published successfully!');
        $this->assertTrue((bool)$lombaOffline->fresh()->is_published);

        // 2. Online Lomba (requires VBG). Without VBG, should fail.
        $lombaOnline = Event::create([
            'title' => 'Lomba Online Publishable',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'location' => 'Online', // online
            'zoom_link' => 'https://zoom.us/xyz',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDay(),
            'until_submission' => now()->addDay(),
            'announcement_date' => now()->addDays(2),
            'until_submission_2' => now()->addDays(4),
            'is_published' => false,
        ]);

        $response2 = $this->actingAs($this->admin)->post(route('admin.events.publish', $lombaOnline));
        $response2->assertRedirect();
        $response2->assertSessionHas('error');
        $this->assertFalse((bool)$lombaOnline->fresh()->is_published);

        // With VBG uploaded, it should succeed.
        $lombaOnline->vbg_path = 'events/docs/vbg.png';
        $lombaOnline->save();

        $response3 = $this->actingAs($this->admin)->post(route('admin.events.publish', $lombaOnline));
        $response3->assertRedirect();
        $response3->assertSessionHas('success', 'Event published successfully!');
        $this->assertTrue((bool)$lombaOnline->fresh()->is_published);
    }

    public function test_user_can_register_for_lomba_after_event_starts_but_before_until_submission_deadline(): void
    {
        $event = Event::create([
            'title' => 'Lomba Ongoing',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'location' => 'Online',
            'event_date' => now()->subDay()->toDateString(), // Started yesterday
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(2),
            'until_submission' => now()->addDay(), // Deadline is tomorrow
            'announcement_date' => now()->addDays(2),
            'until_submission_2' => now()->addDays(4),
            'is_published' => true,
        ]);

        // Access detail page
        $response = $this->actingAs($this->user)->get(route('events.show', $event));
        $response->assertStatus(200);
        // The page should contain the Register option, not "Event Has Started" or "Registration Closed"
        $response->assertSee('Register');
        $response->assertDontSee('Event Has Started');
        $response->assertDontSee('Registration Closed');

        // Verify user can successfully register
        $regResponse = $this->actingAs($this->user)->post(route('events.register', $event));
        $regResponse->assertStatus(200);
        $regResponse->assertJson(['success' => true]);
    }

    public function test_user_cannot_upload_second_submission_if_stage2_payment_pending(): void
    {
        $event = Event::create([
            'title' => 'Lomba Stage 2 Paid',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'price_stage2' => 50000,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDays(3),
            'announcement_date' => now()->subDay(),
            'until_submission_2' => now()->addDay(),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now()->subDays(4),
            'submission_status' => 'lolos',
            'stage2_payment_status' => 'pending', // stage 2 payment required
        ]);

        $file2 = UploadedFile::fake()->create('final_presentation.pdf', 1500, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('events.submit.second', $event), [
            'submission_file_2' => $file2,
        ]);

        $response->assertRedirect(route('events.payment.stage2', $event));
        $response->assertSessionHas('error', 'Harap selesaikan pembayaran Tahap 2 terlebih dahulu sebelum mengunggah submission.');
    }

    public function test_user_can_submit_stage2_manual_payment(): void
    {
        $event = Event::create([
            'title' => 'Lomba Stage 2 Manual',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'price_stage2' => 50000,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDays(3),
            'announcement_date' => now()->subDay(),
            'until_submission_2' => now()->addDay(),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now()->subDays(4),
            'submission_status' => 'lolos',
            'stage2_payment_status' => 'pending',
        ]);

        $paymentProof = UploadedFile::fake()->image('proof.jpg');

        $response = $this->actingAs($this->user)->post(route('events.payment.stage2.manual', $event), [
            'whatsapp'      => '628123456789',
            'payment_proof' => $paymentProof,
        ]);

        $response->assertRedirect(route('events.registered.detail', $event));
        $response->assertSessionHas('success', 'Bukti pembayaran berhasil dikirim. Mohon tunggu konfirmasi admin.');

        $registration->refresh();
        $this->assertEquals('pending', $registration->stage2_payment_status); // remains pending until admin approves

        $payment = \App\Models\ManualPayment::where('event_registration_id', $registration->id)
            ->whereJsonContains('metadata->stage', 2)
            ->first();
        $this->assertNotNull($payment);
        $this->assertEquals('pending', $payment->status);
        $this->assertEquals('628123456789', $payment->whatsapp_number);
    }

    public function test_user_can_get_stage2_midtrans_snap_token(): void
    {
        $event = Event::create([
            'title' => 'Lomba Stage 2 Midtrans',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'price_stage2' => 50000,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDays(3),
            'announcement_date' => now()->subDay(),
            'until_submission_2' => now()->addDay(),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now()->subDays(4),
            'submission_status' => 'lolos',
            'stage2_payment_status' => 'pending',
        ]);

        // Mock Midtrans Snap
        config(['midtrans.server_key' => 'fake_server_key']);
        \Mockery::mock('alias:Midtrans\Snap')
            ->shouldReceive('getSnapToken')
            ->once()
            ->andReturn('fake_snap_token');

        $response = $this->actingAs($this->user)->postJson(route('events.payment.stage2.midtrans', $event), [
            'dial_code' => '+62',
            'whatsapp' => '81234567890',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['snap_token', 'order_id']);
        $this->assertEquals('fake_snap_token', $response->json('snap_token'));

        $payment = \App\Models\ManualPayment::where('event_registration_id', $registration->id)
            ->whereJsonContains('metadata->stage', 2)
            ->first();
        $this->assertNotNull($payment);
        $this->assertEquals('midtrans', $payment->method);
        $this->assertEquals('pending', $payment->status);
        $this->assertEquals('+6281234567890', $payment->whatsapp_number);
    }

    public function test_webhook_can_settle_stage2_midtrans_payment(): void
    {
        $event = Event::create([
            'title' => 'Lomba Stage 2 Webhook',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'price_stage2' => 50000,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDays(3),
            'announcement_date' => now()->subDay(),
            'until_submission_2' => now()->addDay(),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now()->subDays(4),
            'submission_status' => 'lolos',
            'stage2_payment_status' => 'pending',
        ]);

        $payment = \App\Models\ManualPayment::create([
            'event_id' => $event->id,
            'event_registration_id' => $registration->id,
            'user_id' => $this->user->id,
            'order_id' => 'STG2-TEST-123',
            'amount' => 50000,
            'currency' => 'IDR',
            'method' => 'midtrans',
            'status' => 'pending',
            'metadata' => ['stage' => 2],
        ]);

        config(['midtrans.server_key' => 'fake_server_key']);

        // Generate signature key for webhook validation
        $serverKey = 'fake_server_key';
        $signature = hash('sha512', 'STG2-TEST-123' . '200' . '50000' . $serverKey);

        $response = $this->postJson(route('midtrans.notify'), [
            'order_id' => 'STG2-TEST-123',
            'status_code' => '200',
            'gross_amount' => '50000',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
        ]);

        $response->assertStatus(200);

        $payment->refresh();
        $this->assertEquals('settled', $payment->status);

        $registration->refresh();
        $this->assertEquals('settled', $registration->stage2_payment_status);
        $this->assertNotNull($registration->stage2_payment_at);
    }

    public function test_admin_can_approve_stage2_manual_payment(): void
    {
        $event = Event::create([
            'title' => 'Lomba Stage 2 Approve Admin',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'price_stage2' => 50000,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDays(3),
            'announcement_date' => now()->subDay(),
            'until_submission_2' => now()->addDay(),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now()->subDays(4),
            'submission_status' => 'lolos',
            'stage2_payment_status' => 'pending',
        ]);

        $payment = \App\Models\ManualPayment::create([
            'event_id' => $event->id,
            'event_registration_id' => $registration->id,
            'user_id' => $this->user->id,
            'order_id' => 'STG2-APPROVE-123',
            'amount' => 50000,
            'currency' => 'IDR',
            'method' => 'manual_transfer',
            'status' => 'pending',
            'metadata' => ['stage' => 2],
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.events.registrations.approve', [$event, $registration]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Pembayaran Tahap 2 berhasil dikonfirmasi.');

        $payment->refresh();
        $this->assertEquals('settled', $payment->status);

        $registration->refresh();
        $this->assertEquals('settled', $registration->stage2_payment_status);
        $this->assertNotNull($registration->stage2_payment_at);
    }

    public function test_admin_can_reject_stage2_manual_payment(): void
    {
        $event = Event::create([
            'title' => 'Lomba Stage 2 Reject Admin',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'price_stage2' => 50000,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDays(3),
            'announcement_date' => now()->subDay(),
            'until_submission_2' => now()->addDay(),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now()->subDays(4),
            'submission_status' => 'lolos',
            'stage2_payment_status' => 'pending',
        ]);

        $payment = \App\Models\ManualPayment::create([
            'event_id' => $event->id,
            'event_registration_id' => $registration->id,
            'user_id' => $this->user->id,
            'order_id' => 'STG2-REJECT-123',
            'amount' => 50000,
            'currency' => 'IDR',
            'method' => 'manual_transfer',
            'status' => 'pending',
            'metadata' => ['stage' => 2],
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.events.registrations.reject', [$event, $registration]), [
            'rejection_reason' => 'Bukti buram',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Pembayaran Tahap 2 berhasil ditolak.');

        $payment->refresh();
        $this->assertEquals('rejected', $payment->status);
        $this->assertEquals('Bukti buram', $payment->rejection_reason);

        $registration->refresh();
        $this->assertEquals('pending', $registration->stage2_payment_status);
    }

    public function test_admin_event_show_displays_stage2_manual_payment(): void
    {
        $event = Event::create([
            'title' => 'Lomba Stage 2 Display Test',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'price_stage2' => 50000,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDays(3),
            'announcement_date' => now()->subDay(),
            'until_submission_2' => now()->addDay(),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now()->subDays(4),
            'submission_status' => 'lolos',
            'stage2_payment_status' => 'pending',
        ]);

        $payment = \App\Models\ManualPayment::create([
            'event_id' => $event->id,
            'event_registration_id' => $registration->id,
            'user_id' => $this->user->id,
            'order_id' => 'STG2-DISPLAY-123',
            'amount' => 50000,
            'currency' => 'IDR',
            'method' => 'manual_transfer',
            'status' => 'pending',
            'metadata' => ['stage' => 2],
        ]);

        \App\Models\PaymentProof::create([
            'manual_payment_id' => $payment->id,
            'event_registration_id' => $registration->id,
            'file_path' => 'payments/stage2/dummy_proof.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1024,
            'uploaded_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.events.show', $event));
        $response->assertStatus(200);
        $response->assertSee('PROOF TAHAP 2');
        $response->assertSee('Bukti T2');
        $response->assertSee('data-bs-target="#approveModal"', false);
        $response->assertSee('data-bs-target="#rejectModal"', false);
    }

    public function test_user_create_stage2_midtrans_reuses_existing_valid_token(): void
    {
        $event = Event::create([
            'title' => 'Lomba Stage 2 Reuse',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'price_stage2' => 50000,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDays(3),
            'announcement_date' => now()->subDay(),
            'until_submission_2' => now()->addDay(),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now()->subDays(4),
            'submission_status' => 'lolos',
            'stage2_payment_status' => 'pending',
        ]);

        $payment = \App\Models\ManualPayment::create([
            'event_id' => $event->id,
            'event_registration_id' => $registration->id,
            'user_id' => $this->user->id,
            'order_id' => 'STG2-EXISTING-123',
            'amount' => 50000,
            'currency' => 'IDR',
            'method' => 'midtrans',
            'status' => 'pending',
            'whatsapp_number' => '+6281234567890',
            'metadata' => ['stage' => 2, 'snap_token' => 'existing_token_abc'],
        ]);

        $response = $this->actingAs($this->user)->postJson(route('events.payment.stage2.midtrans', $event), [
            'dial_code' => '+62',
            'whatsapp' => '81234567890',
            'force_new' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'snap_token' => 'existing_token_abc',
            'order_id' => 'STG2-EXISTING-123',
        ]);
    }

    public function test_user_can_check_stage2_pending_order(): void
    {
        $event = Event::create([
            'title' => 'Lomba Stage 2 Pending Check',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'price_stage2' => 50000,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDays(3),
            'announcement_date' => now()->subDay(),
            'until_submission_2' => now()->addDay(),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now()->subDays(4),
            'submission_status' => 'lolos',
            'stage2_payment_status' => 'pending',
        ]);

        $payment = \App\Models\ManualPayment::create([
            'event_id' => $event->id,
            'event_registration_id' => $registration->id,
            'user_id' => $this->user->id,
            'order_id' => 'STG2-PENDING-CHECK',
            'amount' => 50000,
            'currency' => 'IDR',
            'method' => 'midtrans',
            'status' => 'pending',
            'whatsapp_number' => '+6281234567890',
            'metadata' => ['stage' => 2, 'snap_token' => 'snap_token_xyz'],
        ]);

        config(['midtrans.server_key' => 'fake_server_key']);
        \Mockery::mock('alias:Midtrans\Transaction')
            ->shouldReceive('status')
            ->with('STG2-PENDING-CHECK')
            ->andReturn([
                'transaction_status' => 'pending',
                'fraud_status' => 'accept'
            ]);

        $response = $this->actingAs($this->user)->getJson(route('events.payment.stage2.pending-order', $event));

        $response->assertStatus(200);
        $response->assertJson([
            'pending' => true,
            'order_id' => 'STG2-PENDING-CHECK',
            'snap_token' => 'snap_token_xyz',
        ]);
    }

    public function test_user_settle_stage2_payment_succeeds_if_settled_on_midtrans(): void
    {
        $event = Event::create([
            'title' => 'Lomba Stage 2 Settle Succeeded',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'price_stage2' => 50000,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDays(3),
            'announcement_date' => now()->subDay(),
            'until_submission_2' => now()->addDay(),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now()->subDays(4),
            'submission_status' => 'lolos',
            'stage2_payment_status' => 'pending',
        ]);

        $payment = \App\Models\ManualPayment::create([
            'event_id' => $event->id,
            'event_registration_id' => $registration->id,
            'user_id' => $this->user->id,
            'order_id' => 'STG2-SETTLE-OK',
            'amount' => 50000,
            'currency' => 'IDR',
            'method' => 'midtrans',
            'status' => 'pending',
            'metadata' => ['stage' => 2],
        ]);

        config(['midtrans.server_key' => 'fake_server_key']);
        \Mockery::mock('alias:Midtrans\Transaction')
            ->shouldReceive('status')
            ->with('STG2-SETTLE-OK')
            ->andReturn([
                'transaction_status' => 'settlement',
                'fraud_status' => 'accept'
            ]);

        $response = $this->actingAs($this->user)->postJson(route('events.payment.stage2.settle', $event), [
            'order_id' => 'STG2-SETTLE-OK'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'settled',
        ]);

        $payment->refresh();
        $this->assertEquals('settled', $payment->status);

        $registration->refresh();
        $this->assertEquals('settled', $registration->stage2_payment_status);
        $this->assertNotNull($registration->stage2_payment_at);
    }

    public function test_user_settle_stage2_payment_stays_pending_if_pending_on_midtrans(): void
    {
        $event = Event::create([
            'title' => 'Lomba Stage 2 Settle Pending',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'price_stage2' => 50000,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDays(3),
            'announcement_date' => now()->subDay(),
            'until_submission_2' => now()->addDay(),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now()->subDays(4),
            'submission_status' => 'lolos',
            'stage2_payment_status' => 'pending',
        ]);

        $payment = \App\Models\ManualPayment::create([
            'event_id' => $event->id,
            'event_registration_id' => $registration->id,
            'user_id' => $this->user->id,
            'order_id' => 'STG2-SETTLE-PENDING',
            'amount' => 50000,
            'currency' => 'IDR',
            'method' => 'midtrans',
            'status' => 'pending',
            'metadata' => ['stage' => 2],
        ]);

        config(['midtrans.server_key' => 'fake_server_key']);
        \Mockery::mock('alias:Midtrans\Transaction')
            ->shouldReceive('status')
            ->with('STG2-SETTLE-PENDING')
            ->andReturn([
                'transaction_status' => 'pending',
                'fraud_status' => 'accept'
            ]);

        $response = $this->actingAs($this->user)->postJson(route('events.payment.stage2.settle', $event), [
            'order_id' => 'STG2-SETTLE-PENDING'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'status' => 'pending',
        ]);

        $payment->refresh();
        $this->assertEquals('pending', $payment->status);

        $registration->refresh();
        $this->assertEquals('pending', $registration->stage2_payment_status);
        $this->assertNull($registration->stage2_payment_at);
    }

    public function test_user_cannot_access_or_pay_stage2_outside_payment_dates(): void
    {
        $event = Event::create([
            'title' => 'Lomba Stage 2 Date Restrictions',
            'jenis' => 'Lomba',
            'description' => 'Desc',
            'speaker' => '',
            'price' => 0,
            'price_stage2' => 50000,
            'location' => 'Online',
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '10:00:00',
            'start_submission' => now()->subDays(5),
            'until_submission' => now()->subDays(3),
            'announcement_date' => now()->subDay(),
            'until_submission_2' => now()->addDay(),
            'finalist_payment_start' => now()->addMinutes(10), // in the future
            'finalist_payment_end' => now()->addHour(),
            'is_published' => true,
        ]);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->user->id,
            'status' => 'active',
            'submission_path' => 'submissions/proposal.pdf',
            'submission_uploaded_at' => now()->subDays(4),
            'submission_status' => 'lolos', // qualified!
            'stage2_payment_status' => 'pending',
        ]);

        // Scenario 1: Payment not started yet
        $startStr = $event->finalist_payment_start->translatedFormat('d M Y, H:i');
        $expectedNotOpenMsg = 'Stage 2 Payment / Finalist Registration is not open yet. It will open on ' . $startStr . ' WIB.';

        // 1.1 access payment page -> redirect to detail
        $response = $this->actingAs($this->user)->get(route('events.payment.stage2', $event));
        $response->assertRedirect(route('events.registered.detail', $event));
        $response->assertSessionHas('error', $expectedNotOpenMsg);

        // 1.2 submit manual payment -> redirect to detail
        $response2 = $this->actingAs($this->user)->post(route('events.payment.stage2.manual', $event), [
            'whatsapp' => '08123456789',
            'payment_proof' => \Illuminate\Http\UploadedFile::fake()->create('proof.jpg', 100),
        ]);
        $response2->assertRedirect(route('events.registered.detail', $event));
        $response2->assertSessionHas('error', $expectedNotOpenMsg);

        // 1.3 midtrans snap token request -> 422 JSON
        $response3 = $this->actingAs($this->user)->postJson(route('events.payment.stage2.midtrans', $event));
        $response3->assertStatus(422);
        $response3->assertJson(['error' => $expectedNotOpenMsg]);

        // Scenario 2: Payment has ended
        $event->update([
            'finalist_payment_start' => now()->subHours(2),
            'finalist_payment_end' => now()->subHour(), // ended
        ]);
        $endStr = $event->finalist_payment_end->translatedFormat('d M Y, H:i');
        $expectedClosedMsg = 'Stage 2 Payment / Finalist Registration is closed. It ended on ' . $endStr . ' WIB.';

        // 2.1 access payment page -> redirect to detail
        $response = $this->actingAs($this->user)->get(route('events.payment.stage2', $event));
        $response->assertRedirect(route('events.registered.detail', $event));
        $response->assertSessionHas('error', $expectedClosedMsg);

        // 2.2 submit manual payment -> redirect to detail
        $response2 = $this->actingAs($this->user)->post(route('events.payment.stage2.manual', $event), [
            'whatsapp' => '08123456789',
            'payment_proof' => \Illuminate\Http\UploadedFile::fake()->create('proof.jpg', 100),
        ]);
        $response2->assertRedirect(route('events.registered.detail', $event));
        $response2->assertSessionHas('error', $expectedClosedMsg);

        // 2.3 midtrans snap token request -> 422 JSON
        $response3 = $this->actingAs($this->user)->postJson(route('events.payment.stage2.midtrans', $event));
        $response3->assertStatus(422);
        $response3->assertJson(['error' => $expectedClosedMsg]);
    }
}

