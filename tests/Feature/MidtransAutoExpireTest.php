<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EventRegistration;
use App\Models\ManualPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransAutoExpireTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->event = Event::create([
            'title' => 'Paid Event',
            'speaker' => 'Speaker Test',
            'description' => 'Test event description',
            'location' => 'Online',
            'price' => 10000,
            'event_time' => '10:00:00',
            'event_date' => now()->addDay()->toDateString(),
            'accept_online_payment' => true,
            'accept_manual_transfer' => false,
            'is_published' => true,
        ]);
    }

    public function test_snap_token_creation_without_custom_expiry(): void
    {
        config(['midtrans.server_key' => 'fake_server_key']);

        \Mockery::mock('alias:Midtrans\Snap')
            ->shouldReceive('getSnapToken')
            ->once()
            ->with(\Mockery::on(function ($params) {
                return !isset($params['expiry']);
            }))
            ->andReturn('fake_snap_token_123');

        $response = $this->actingAs($this->user)->getJson(route('payment.snap-token', $this->event->id));

        $response->assertStatus(200);
        $response->assertJson(['snap_token' => 'fake_snap_token_123']);
    }

    public function test_pending_order_check_expires_payment_when_time_limit_passed(): void
    {
        config(['midtrans.server_key' => 'fake_server_key']);

        $registration = EventRegistration::create([
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'status' => 'pending',
            'registration_code' => 'EVT-123',
            'total_price' => 10000,
        ]);

        // Create a manual payment that is older than 5 minutes
        $payment = ManualPayment::create([
            'event_id' => $this->event->id,
            'event_registration_id' => $registration->id,
            'user_id' => $this->user->id,
            'order_id' => 'MT-EVT-OLD',
            'amount' => 10000,
            'currency' => 'IDR',
            'method' => 'midtrans',
            'status' => 'pending',
            'metadata' => [
                'snap_token' => 'token_old',
                'snap_token_created_at' => now()->subMinutes(6)->toIso8601String(),
            ],
        ]);

        // Midtrans transaction status throws 404 (user never completed payment method selection)
        \Mockery::mock('alias:Midtrans\Transaction')
            ->shouldReceive('status')
            ->with('MT-EVT-OLD')
            ->once()
            ->andThrow(new \Exception('Midtrans API is returning 404: Not Found'));

        $response = $this->actingAs($this->user)->getJson(route('payment.pending-order', $this->event->id));

        $response->assertStatus(200);
        $response->assertJson([
            'pending' => false,
            'needs_force_new' => true,
        ]);

        $payment->refresh();
        $this->assertEquals('expired', $payment->status);

        $registration->refresh();
        $this->assertEquals('expired', $registration->status);
    }

    public function test_pending_order_check_remains_pending_when_within_time_limit(): void
    {
        config(['midtrans.server_key' => 'fake_server_key']);

        $registration = EventRegistration::create([
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'status' => 'pending',
            'registration_code' => 'EVT-123',
            'total_price' => 10000,
        ]);

        // Create a manual payment that is 2 minutes old
        $payment = ManualPayment::create([
            'event_id' => $this->event->id,
            'event_registration_id' => $registration->id,
            'user_id' => $this->user->id,
            'order_id' => 'MT-EVT-NEW',
            'amount' => 10000,
            'currency' => 'IDR',
            'method' => 'midtrans',
            'status' => 'pending',
            'metadata' => [
                'snap_token' => 'token_new',
                'snap_token_created_at' => now()->subMinutes(2)->toIso8601String(),
            ],
        ]);

        \Mockery::mock('alias:Midtrans\Transaction')
            ->shouldReceive('status')
            ->with('MT-EVT-NEW')
            ->once()
            ->andThrow(new \Exception('404 Not Found'));

        $response = $this->actingAs($this->user)->getJson(route('payment.pending-order', $this->event->id));

        $response->assertStatus(200);
        $response->assertJson([
            'pending' => true,
            'order_id' => 'MT-EVT-NEW',
            'snap_token' => 'token_new',
        ]);

        $payment->refresh();
        $this->assertEquals('pending', $payment->status);
    }
}
