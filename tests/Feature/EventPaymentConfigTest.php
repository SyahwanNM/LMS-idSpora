<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Illuminate\Http\UploadedFile;

class EventPaymentConfigTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_create_event_with_online_and_manual_payments(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.events.store'), [
            'title' => 'Event Pembayaran Keduanya',
            'speaker' => 'Speaker Test',
            'speakers' => ['Speaker Test'],
            'speaker_salaries' => [0],
            'manage_action' => 'manage',
            'short_description' => 'Short desc',
            'description' => 'Long desc',
            'location' => 'Online',
            'location_mode' => 'online',
            'price' => 50000,
            'event_date' => now()->addDay()->toDateString(),
            'event_time' => '10:00',
            'accept_online_payment' => '1',
            'accept_manual_transfer' => '1',
            'is_reseller_event' => '1',
            'bank_name' => 'Bank BNI',
            'bank_account_number' => '1111-999-236',
            'bank_account_holder' => 'a.n. APTIKOM JABAR',
            'image' => UploadedFile::fake()->image('event.jpg'),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $event = Event::where('title', 'Event Pembayaran Keduanya')->first();
        $this->assertNotNull($event);
        $this->assertTrue($event->accept_online_payment);
        $this->assertTrue($event->accept_manual_transfer);
        $this->assertTrue($event->is_reseller_event);
        $this->assertEquals('Bank BNI', $event->bank_name);
        $this->assertEquals('1111-999-236', $event->bank_account_number);
        $this->assertEquals('a.n. APTIKOM JABAR', $event->bank_account_holder);
    }

    public function test_validation_fails_if_paid_event_has_no_payment_method(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.events.store'), [
            'title' => 'Event Tanpa Pembayaran',
            'speaker' => 'Speaker Test',
            'speakers' => ['Speaker Test'],
            'speaker_salaries' => [0],
            'manage_action' => 'manage',
            'short_description' => 'Short desc',
            'description' => 'Long desc',
            'location' => 'Online',
            'location_mode' => 'online',
            'price' => 50000,
            'event_date' => now()->addDay()->toDateString(),
            'event_time' => '10:00',
            'image' => UploadedFile::fake()->image('event.jpg'),
            // neither payment method is sent, controller sets both as false
        ]);

        $response->assertSessionHasErrors(['accept_online_payment']);
    }

    public function test_validation_fails_if_manual_transfer_is_enabled_but_bank_details_are_missing(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.events.store'), [
            'title' => 'Event Manual Kurang Data',
            'speaker' => 'Speaker Test',
            'speakers' => ['Speaker Test'],
            'speaker_salaries' => [0],
            'manage_action' => 'manage',
            'short_description' => 'Short desc',
            'description' => 'Long desc',
            'location' => 'Online',
            'location_mode' => 'online',
            'price' => 50000,
            'event_date' => now()->addDay()->toDateString(),
            'event_time' => '10:00',
            'accept_manual_transfer' => '1',
            'image' => UploadedFile::fake()->image('event.jpg'),
            // Missing bank details fields
        ]);

        $response->assertSessionHasErrors(['bank_name', 'bank_account_number', 'bank_account_holder']);
    }

    public function test_admin_can_update_event_payment_methods(): void
    {
        // 1. Create base event
        $event = Event::create([
            'title' => 'Event Update Payment',
            'image' => 'uploads/events/test.jpg',
            'speaker' => 'Speaker Test',
            'description' => 'Test event description',
            'location' => 'Online',
            'price' => 100000,
            'event_time' => '10:00:00',
            'event_date' => now()->addDay()->toDateString(),
            'accept_online_payment' => true,
            'accept_manual_transfer' => false,
            'is_reseller_event' => false,
        ]);

        // 2. Perform update
        $response = $this->actingAs($this->admin)->put(route('admin.events.update', $event), [
            'title' => 'Event Update Payment',
            'speaker' => 'Speaker Test',
            'speakers' => ['Speaker Test'],
            'speaker_salaries' => [0],
            'manage_action' => 'manage',
            'short_description' => 'Short desc',
            'description' => 'Long desc',
            'location' => 'Online',
            'location_mode' => 'online',
            'price' => 100000,
            'event_date' => now()->addDay()->toDateString(),
            'event_time' => '10:00',
            'accept_online_payment' => '0',
            'accept_manual_transfer' => '1',
            'is_reseller_event' => '1',
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '987654321',
            'bank_account_holder' => 'a.n. Mandiri Admin',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $event->refresh();
        $this->assertFalse($event->accept_online_payment);
        $this->assertTrue($event->accept_manual_transfer);
        $this->assertTrue($event->is_reseller_event);
        $this->assertEquals('Bank Mandiri', $event->bank_name);
        $this->assertEquals('987654321', $event->bank_account_number);
        $this->assertEquals('a.n. Mandiri Admin', $event->bank_account_holder);
    }

    public function test_referral_code_applies_discount_for_manual_registration(): void
    {
        $buyer = User::factory()->create();
        $reseller = User::factory()->create([
            'referral_code' => 'RESELLER123',
        ]);

        $event = Event::create([
            'title' => 'Paid Event with Reseller',
            'image' => 'uploads/events/test.jpg',
            'speaker' => 'Speaker Test',
            'description' => 'Test event description',
            'location' => 'Offline',
            'price' => 100000,
            'event_time' => '10:00:00',
            'event_date' => now()->addDay()->toDateString(),
            'accept_online_payment' => false,
            'accept_manual_transfer' => true,
            'is_reseller_event' => true,
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '123456',
            'bank_account_holder' => 'Holder Name',
        ]);

        $response = $this->actingAs($buyer)->post(route('payment.manual.register', $event->id), [
            'payment_method' => 'transfer',
            'full_name' => $buyer->name,
            'whatsapp' => '081234567890',
            'university_origin' => 'Telkom University',
            'study_program' => 'Teknik',
            'position' => 'Mahasiswa',
            'referral_code' => 'RESELLER123',
            'payment_proof' => UploadedFile::fake()->image('proof.jpg'),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $registration = \App\Models\EventRegistration::where('user_id', $buyer->id)
            ->where('event_id', $event->id)
            ->first();

        $this->assertNotNull($registration);
        $this->assertEquals(90000, $registration->total_price);

        $payment = \App\Models\ManualPayment::where('event_registration_id', $registration->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(90000, $payment->amount);
        $this->assertEquals('RESELLER123', $payment->referral_code);
    }

    public function test_individual_registration_captures_and_saves_team_name(): void
    {
        $buyer = User::factory()->create();

        $event = Event::create([
            'title' => 'Lomba Individu Test',
            'image' => 'uploads/events/test.jpg',
            'speaker' => 'Speaker Test',
            'description' => 'Test event description',
            'location' => 'Offline',
            'price' => 100000,
            'event_time' => '10:00:00',
            'event_date' => now()->addDay()->toDateString(),
            'accept_online_payment' => false,
            'accept_manual_transfer' => true,
            'is_reseller_event' => false,
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '123456',
            'bank_account_holder' => 'Holder Name',
        ]);

        $response = $this->actingAs($buyer)->post(route('payment.manual.register', $event->id), [
            'payment_method' => 'transfer',
            'full_name' => $buyer->name,
            'whatsapp' => '081234567890',
            'university_origin' => 'Telkom University',
            'study_program' => 'Teknik',
            'position' => 'Mahasiswa',
            'team_name' => 'Team Alpha Omega',
            'payment_proof' => UploadedFile::fake()->image('proof.jpg'),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $registration = \App\Models\EventRegistration::where('user_id', $buyer->id)
            ->where('event_id', $event->id)
            ->first();

        $this->assertNotNull($registration);
        $this->assertEquals('Team Alpha Omega', $registration->team_name);
    }
}
