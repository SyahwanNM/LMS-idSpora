<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventDailyQr;
use App\Models\EventDailyAttendance;
use Illuminate\Support\Facades\DB;

class EventDailyAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_admin_can_access_attendance_stats_and_toggle_attendance()
    {
        // 1. Setup Users
        $admin = User::factory()->create(['role' => 'admin']);
        $participant = User::factory()->create(['role' => 'user']);

        // 2. Setup Event (Multi-day event)
        $event = Event::create([
            'title' => 'Test PHP Event',
            'image' => 'placeholder.png',
            'speaker' => 'John Doe',
            'description' => 'Test Description',
            'location' => 'Jakarta',
            'price' => 0.00,
            'event_time' => '09:00:00',
            'event_date' => now()->format('Y-m-d'),
            'event_until_date' => now()->addDays(2)->format('Y-m-d'), // 3-day event
        ]);

        // 3. Setup QRs
        $dailyQr = EventDailyQr::create([
            'event_id' => $event->id,
            'qr_date' => now()->format('Y-m-d'),
            'day_number' => 1,
            'token' => 'day1token',
            'qr_image' => 'qr1.png',
        ]);

        // 4. Setup Registration
        $registration = EventRegistration::create([
            'user_id' => $participant->id,
            'event_id' => $event->id,
            'status' => 'active',
        ]);

        // 5. Get Attendance Stats as Admin
        $response = $this->actingAs($admin)
            ->getJson(route('admin.events.attendance.stats', $event));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'total_active_participants' => 1,
            ]);

        // 6. Perform Manual Check-In Day 1
        $response = $this->actingAs($admin)
            ->postJson(route('admin.events.registrations.manual-check-in', [$event, $registration]), [
                'day_number' => 1,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        // Verify Daily Attendance record exists
        $this->assertTrue(EventDailyAttendance::where('event_registration_id', $registration->id)->where('day_number', 1)->exists());

        // Verify Registration attendance status is updated
        $registration->refresh();
        $this->assertEquals('yes', $registration->attendance_status);

        // 7. Cancel Day 1 Attendance
        $response = $this->actingAs($admin)
            ->postJson(route('admin.events.registrations.cancel-day', [$event, $registration]), [
                'day_number' => 1,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        // Verify Daily Attendance record is deleted
        $this->assertFalse(EventDailyAttendance::where('event_registration_id', $registration->id)->where('day_number', 1)->exists());

        // Verify Registration attendance status reverted
        $registration->refresh();
        $this->assertEquals('no', $registration->attendance_status);
    }

    public function test_unauthorized_user_cannot_access_or_toggle_attendance()
    {
        $user = User::factory()->create(['role' => 'user']);
        $event = Event::create([
            'title' => 'Test PHP Event 2',
            'image' => 'placeholder.png',
            'speaker' => 'John Doe',
            'description' => 'Test Description',
            'location' => 'Jakarta',
            'price' => 0.00,
            'event_time' => '09:00:00',
            'event_date' => now()->format('Y-m-d'),
        ]);
        $registration = EventRegistration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'active',
        ]);

        // Regular user should get forbidden / redirected by AdminMiddleware (403 for json)
        $response = $this->actingAs($user)
            ->getJson(route('admin.events.attendance.stats', $event));

        $response->assertStatus(403);
    }
}
