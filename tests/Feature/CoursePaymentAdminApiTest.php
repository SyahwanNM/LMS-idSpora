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
}
