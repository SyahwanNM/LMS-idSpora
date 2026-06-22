<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SupportMessage;
use App\Mail\SupportTicketStatusMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SupportTicketEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_sent_when_support_ticket_resolved()
    {
        Mail::fake();

        // Create admin user
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Create support message
        $ticket = SupportMessage::create([
            'name' => 'Tester User',
            'email' => 'tester@example.com',
            'type' => 'kendala',
            'subject' => 'Aplikasi Error',
            'message' => 'Saya tidak bisa login.',
            'status' => 'new',
        ]);

        // Post status update
        $response = $this->post(route('admin.crm.support.updateStatus', $ticket), [
            'status' => 'resolved',
        ]);

        $response->assertStatus(302);
        
        // Assert ticket status updated
        $this->assertEquals('resolved', $ticket->fresh()->status);

        // Assert mail was sent to correct user
        Mail::assertSent(SupportTicketStatusMail::class, function ($mail) use ($ticket) {
            return $mail->hasTo('tester@example.com') && $mail->ticket->id === $ticket->id;
        });
    }

    public function test_email_sent_when_support_ticket_ignored()
    {
        Mail::fake();

        // Create admin user
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Create support message
        $ticket = SupportMessage::create([
            'name' => 'Tester User',
            'email' => 'tester@example.com',
            'type' => 'kendala',
            'subject' => 'Aplikasi Error',
            'message' => 'Saya tidak bisa login.',
            'status' => 'new',
        ]);

        // Post status update
        $response = $this->post(route('admin.crm.support.updateStatus', $ticket), [
            'status' => 'ignored',
        ]);

        $response->assertStatus(302);
        
        // Assert ticket status updated
        $this->assertEquals('ignored', $ticket->fresh()->status);

        // Assert mail was sent to correct user
        Mail::assertSent(SupportTicketStatusMail::class, function ($mail) use ($ticket) {
            return $mail->hasTo('tester@example.com') && $mail->ticket->id === $ticket->id;
        });
    }

    public function test_email_not_sent_when_support_ticket_processed()
    {
        Mail::fake();

        // Create admin user
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Create support message
        $ticket = SupportMessage::create([
            'name' => 'Tester User',
            'email' => 'tester@example.com',
            'type' => 'kendala',
            'subject' => 'Aplikasi Error',
            'message' => 'Saya tidak bisa login.',
            'status' => 'new',
        ]);

        // Post status update
        $response = $this->post(route('admin.crm.support.updateStatus', $ticket), [
            'status' => 'processed',
        ]);

        $response->assertStatus(302);
        
        // Assert ticket status updated
        $this->assertEquals('processed', $ticket->fresh()->status);

        // Assert mail was not sent
        Mail::assertNotSent(SupportTicketStatusMail::class);
    }
}
