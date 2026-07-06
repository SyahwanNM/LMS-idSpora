<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\ManualPayment;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualPaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $trainer;
    private User $student;
    private User $reseller;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->trainer = User::factory()->create(['role' => 'trainer', 'wallet_balance' => 0]);
        $this->student = User::factory()->create(['role' => 'user']);
        
        $this->reseller = User::factory()->create([
            'role' => 'user',
            'referral_code' => 'RESELLER123',
            'wallet_balance' => 0
        ]);

        $this->category = Category::create([
            'name' => 'IT & Software',
            'description' => 'IT & Software Category',
        ]);
    }

    /**
     * Helper to set total referrals for reseller to simulate a level.
     */
    private function mockReferralsCount(User $reseller, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            Referral::create([
                'user_id' => $reseller->id,
                'referred_user_id' => $this->student->id,
                'amount' => 10000,
                'status' => 'paid',
                'description' => 'Mock Referral ' . $i
            ]);
        }
    }

    public function test_course_manual_payment_approval_distributes_trainer_and_reseller_bronze_commission(): void
    {
        // 1. Setup course with reseller enabled and trainer revenue share
        $course = Course::create([
            'name' => 'PHP Laravel Mastery',
            'category_id' => $this->category->id,
            'price' => 200000,
            'trainer_id' => $this->trainer->id,
            'trainer_revenue_percent' => 25,
            'is_reseller_course' => true,
            'reseller_commission_bronze' => 10,
            'reseller_commission_silver' => 12,
            'reseller_commission_gold' => 15,
            'status' => 'approved',
        ]);

        // Bronze level: 0 referrals
        $this->mockReferralsCount($this->reseller, 0);

        $enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $course->id,
            'status' => 'pending',
        ]);

        $payment = ManualPayment::create([
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
            'user_id' => $this->student->id,
            'order_id' => 'TRF-CRS-TEST-1',
            'amount' => 180000, // after 10% referral discount
            'currency' => 'IDR',
            'method' => 'manual_transfer',
            'referral_code' => 'RESELLER123',
            'status' => 'pending',
        ]);

        // 2. Approve payment as admin (web controller flow)
        $this->actingAs($this->admin);
        $response = $this->post(route('admin.courses.manual-payments.approve', [$course->id, $payment->id]));

        $response->assertRedirect();
        $payment->refresh();
        $this->assertEquals('settled', $payment->status);

        $enrollment->refresh();
        $this->assertEquals('active', $enrollment->status);

        // 3. Verify trainer balance (25% of 180,000 = 45,000)
        $this->trainer->refresh();
        $this->assertEquals(45000, (float)$this->trainer->wallet_balance);
        $this->assertDatabaseHas('trainer_notifications', [
            'trainer_id' => $this->trainer->id,
            'type' => 'revenue_share',
        ]);

        // 4. Verify reseller balance (Bronze: 10% of 180,000 = 18,000)
        $this->reseller->refresh();
        $this->assertEquals(18000, (float)$this->reseller->wallet_balance);
        $this->assertDatabaseHas('referrals', [
            'user_id' => $this->reseller->id,
            'referred_user_id' => $this->student->id,
            'amount' => 18000,
            'status' => 'paid',
            'description' => 'Komisi Course: ' . $course->name,
        ]);
    }

    public function test_course_manual_payment_approval_distributes_silver_commission(): void
    {
        $course = Course::create([
            'name' => 'PHP Laravel Mastery',
            'category_id' => $this->category->id,
            'price' => 200000,
            'is_reseller_course' => true,
            'reseller_commission_bronze' => 10,
            'reseller_commission_silver' => 12,
            'reseller_commission_gold' => 15,
            'status' => 'approved',
        ]);

        // Silver level: 51 referrals
        $this->mockReferralsCount($this->reseller, 51);

        $enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $course->id,
            'status' => 'pending',
        ]);

        $payment = ManualPayment::create([
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
            'user_id' => $this->student->id,
            'order_id' => 'TRF-CRS-TEST-2',
            'amount' => 180000,
            'currency' => 'IDR',
            'method' => 'manual_transfer',
            'referral_code' => 'RESELLER123',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin);
        $response = $this->post(route('admin.courses.manual-payments.approve', [$course->id, $payment->id]));

        // Verify reseller balance (Silver: 12% of 180,000 = 21,600)
        $this->reseller->refresh();
        $this->assertDatabaseHas('referrals', [
            'user_id' => $this->reseller->id,
            'referred_user_id' => $this->student->id,
            'amount' => 21600,
            'status' => 'paid',
            'description' => 'Komisi Course: ' . $course->name,
        ]);
    }

    public function test_course_manual_payment_approval_distributes_gold_commission(): void
    {
        $course = Course::create([
            'name' => 'PHP Laravel Mastery',
            'category_id' => $this->category->id,
            'price' => 200000,
            'is_reseller_course' => true,
            'reseller_commission_bronze' => 10,
            'reseller_commission_silver' => 12,
            'reseller_commission_gold' => 15,
            'status' => 'approved',
        ]);

        // Gold level: 151 referrals
        $this->mockReferralsCount($this->reseller, 151);

        $enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $course->id,
            'status' => 'pending',
        ]);

        $payment = ManualPayment::create([
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
            'user_id' => $this->student->id,
            'order_id' => 'TRF-CRS-TEST-3',
            'amount' => 180000,
            'currency' => 'IDR',
            'method' => 'manual_transfer',
            'referral_code' => 'RESELLER123',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin);
        $response = $this->post(route('admin.courses.manual-payments.approve', [$course->id, $payment->id]));

        // Verify reseller balance (Gold: 15% of 180,000 = 27,000)
        $this->assertDatabaseHas('referrals', [
            'user_id' => $this->reseller->id,
            'referred_user_id' => $this->student->id,
            'amount' => 27000,
            'status' => 'paid',
            'description' => 'Komisi Course: ' . $course->name,
        ]);
    }

    public function test_api_course_payment_approval_distributes_silver_commission(): void
    {
        $course = Course::create([
            'name' => 'API Course Test',
            'category_id' => $this->category->id,
            'price' => 100000,
            'is_reseller_course' => true,
            'reseller_commission_bronze' => 10,
            'reseller_commission_silver' => 12,
            'reseller_commission_gold' => 15,
            'status' => 'approved',
        ]);

        $this->mockReferralsCount($this->reseller, 60); // Silver level

        $payment = ManualPayment::create([
            'course_id' => $course->id,
            'user_id' => $this->student->id,
            'order_id' => 'TRF-CRS-API-1',
            'amount' => 90000,
            'currency' => 'IDR',
            'method' => 'manual_transfer',
            'referral_code' => 'RESELLER123',
            'status' => 'pending',
        ]);

        $adminUser = User::factory()->create(['role' => 'admin']);
        \Laravel\Sanctum\Sanctum::actingAs($adminUser);

        $response = $this->postJson("/api/admin/course-payments/{$payment->id}/approve");
        $response->assertOk();

        // Silver: 12% of 90,000 = 10,800
        $this->assertDatabaseHas('referrals', [
            'user_id' => $this->reseller->id,
            'referred_user_id' => $this->student->id,
            'amount' => 10800,
            'status' => 'paid',
            'description' => 'Komisi Course: ' . $course->name,
        ]);
    }

    public function test_event_registration_manual_approval_distributes_reseller_commission(): void
    {
        // 1. Create event with reseller enabled
        $event = Event::create([
            'title' => 'National Tech Conference',
            'speaker' => 'Speaker Test',
            'description' => 'A big tech conference',
            'price' => 300000,
            'is_reseller_event' => true,
            'reseller_commission_bronze' => 10,
            'reseller_commission_silver' => 12,
            'reseller_commission_gold' => 15,
            'event_date' => now()->addDays(5)->toDateString(),
            'event_time' => '09:00:00',
            'location' => 'Jakarta',
            'quota' => 100,
            'banner' => 'events/banner.jpg',
            'schedule' => json_encode(['Day 1' => 'Intro']),
        ]);

        $this->mockReferralsCount($this->reseller, 55); // Silver level: 12% commission

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $this->student->id,
            'status' => 'pending',
        ]);

        $payment = ManualPayment::create([
            'event_registration_id' => $registration->id,
            'user_id' => $this->student->id,
            'order_id' => 'TRF-EVT-TEST-1',
            'amount' => 270000, // 10% referral discount applied on purchase
            'currency' => 'IDR',
            'method' => 'manual_transfer',
            'referral_code' => 'RESELLER123',
            'status' => 'pending',
        ]);

        // 2. Approve event registration as admin
        $this->actingAs($this->admin);
        $response = $this->post(route('admin.events.registrations.approve', [$event->id, $registration->id]));

        $response->assertRedirect();
        
        $payment->refresh();
        $this->assertEquals('settled', $payment->status);

        $registration->refresh();
        $this->assertEquals('active', $registration->status);

        // 3. Verify reseller balance (Silver: 12% of 270,000 = 32,400)
        $this->assertDatabaseHas('referrals', [
            'user_id' => $this->reseller->id,
            'referred_user_id' => $this->student->id,
            'amount' => 32400,
            'status' => 'paid',
            'description' => 'Komisi Event: ' . $event->title,
        ]);
    }
}
