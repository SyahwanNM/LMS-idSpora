<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CoursePaymentAdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_course_payments_index(): void
    {
        $this->getJson('/api/admin/course-payments')
            ->assertStatus(401);
    }

    public function test_non_admin_cannot_access_admin_course_payments_index(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/admin/course-payments')
            ->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Forbidden',
            ]);
    }

    public function test_admin_can_access_admin_course_payments_index(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/course-payments')
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Daftar pembayaran course',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'current_page',
                    'data',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ],
            ]);
    }

    public function test_admin_can_approve_course_payment_and_distribute_trainer_revenue(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $trainer = User::factory()->create(['role' => 'trainer', 'wallet_balance' => 0]);
        $student = User::factory()->create(['role' => 'user']);

        $category = \App\Models\Category::create([
            'name' => 'UI/UX Design',
            'description' => 'UI/UX Design Category',
        ]);

        $course = \App\Models\Course::create([
            'name' => 'UI/UX Design Course',
            'category_id' => $category->id,
            'price' => 500000,
            'trainer_id' => $trainer->id,
            'trainer_revenue_percent' => 30,
            'status' => 'approved',
        ]);

        $payment = \App\Models\ManualPayment::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'order_id' => 'INV-CRS-12345',
            'amount' => 500000,
            'status' => 'pending',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson("/api/admin/course-payments/{$payment->id}/approve");

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'message' => 'Pembayaran course berhasil di-approve dan invoice dikirim ke email user',
        ]);

        $payment->refresh();
        $this->assertEquals('settled', $payment->status);

        // Verify trainer balance is updated: 30% of 500,000 is 150,000
        $trainer->refresh();
        $this->assertEquals(150000, (float)$trainer->wallet_balance);

        // Verify trainer notification was created
        $this->assertDatabaseHas('trainer_notifications', [
            'trainer_id' => $trainer->id,
            'type' => 'revenue_share',
        ]);
    }
}
