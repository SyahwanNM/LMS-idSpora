<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminUsersApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_users_index(): void
    {
        $this->getJson('/api/admin/users')->assertStatus(401);
    }

    public function test_non_admin_cannot_access_admin_users_index(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $this->getJson('/api/admin/users')
            ->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Forbidden',
            ]);
    }

    public function test_admin_can_access_admin_users_index_and_show(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['role' => 'user']);

        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/users')
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Daftar user (admin)',
            ]);

        $this->getJson('/api/admin/users/' . $target->id)
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Detail user (admin)',
            ])
            ->assertJsonPath('data.id', $target->id);
    }
}
