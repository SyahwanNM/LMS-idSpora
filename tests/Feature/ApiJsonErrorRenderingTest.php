<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiJsonErrorRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_404_route_not_found_returns_json_even_without_accept_header(): void
    {
        $response = $this->get('/api/this-route-does-not-exist');

        $response->assertStatus(404);
        $this->assertStringContainsString('application/json', (string) $response->headers->get('Content-Type'));
        $response->assertJson([
            'status' => 'error',
            'message' => 'Route not found',
        ]);
    }

    public function test_api_model_not_found_returns_json_even_without_accept_header(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->get('/api/admin/events/999999');

        $response->assertStatus(404);
        $this->assertStringContainsString('application/json', (string) $response->headers->get('Content-Type'));
        $response->assertJson([
            'status' => 'error',
            'message' => 'Resource not found',
        ]);
    }
}
