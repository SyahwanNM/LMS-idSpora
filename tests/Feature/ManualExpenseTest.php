<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManualExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_manual_expense_with_proof_and_gets_auto_approved(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $file = UploadedFile::fake()->image('proof.jpg');

        $response = $this->post(route('admin.finance.store-expense'), [
            'description' => 'Test Expense',
            'amount' => 150000,
            'expense_date' => '2026-06-26',
            'category' => 'Operasional',
            'proof_of_payment' => $file
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Pengeluaran manual berhasil dicatat dengan bukti pembayaran.');

        $expense = Expense::first();
        $this->assertNotNull($expense);
        $this->assertEquals('Test Expense', $expense->description);
        $this->assertEquals(150000, $expense->amount);
        $this->assertEquals('approved', $expense->status);
        $this->assertNotNull($expense->proof_of_payment);

        // Verify the file was stored
        Storage::disk('public')->assertExists($expense->proof_of_payment);
    }

    public function test_admin_can_store_manual_expense_via_api_with_proof_and_gets_auto_approved(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        \Laravel\Sanctum\Sanctum::actingAs($admin);

        $file = UploadedFile::fake()->image('proof_api.jpg');

        $response = $this->postJson('/api/admin/finance/expenses', [
            'description' => 'Test API Expense',
            'amount' => 250000,
            'expense_date' => '2026-06-26',
            'category' => 'Operasional',
            'proof_of_payment' => $file
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Pengeluaran manual berhasil dicatat dengan bukti pembayaran.',
        ]);

        $expense = Expense::where('description', 'Test API Expense')->first();
        $this->assertNotNull($expense);
        $this->assertEquals(250000, $expense->amount);
        $this->assertEquals('approved', $expense->status);
        $this->assertNotNull($expense->proof_of_payment);

        // Verify the file was stored
        Storage::disk('public')->assertExists($expense->proof_of_payment);
    }

    public function test_admin_can_approve_event_expense(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $event = \App\Models\Event::create([
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

        $ee = \App\Models\EventExpense::create([
            'event_id' => $event->id,
            'item' => 'Rent Room',
            'quantity' => 1,
            'unit_price' => 500000,
            'total' => 500000,
            'status' => 'pending'
        ]);

        $this->actingAs($admin);

        $file = UploadedFile::fake()->image('proof_event.jpg');

        $response = $this->post(route('admin.finance.event-expense.approve', $ee->id), [
            'proof_of_payment' => $file
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Cost event berhasil disetujui.');

        $ee->refresh();
        $this->assertEquals('approved', $ee->status);
        $this->assertNotNull($ee->proof_of_payment);

        // Verify the file was stored
        Storage::disk('public')->assertExists($ee->proof_of_payment);
    }
}
