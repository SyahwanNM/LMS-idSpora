<?php

namespace App\Mail;

use App\Models\Broadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;

class CRMBlastMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $broadcast;

    /**
     * Create a new message instance.
     */
    public function __construct(Broadcast $broadcast)
    {
        $this->broadcast = $broadcast;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->broadcast->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.crm.blast',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $mailAttachments = [];
        if ($this->broadcast->attachment) {
            $paths = json_decode($this->broadcast->attachment, true);
            if (is_array($paths)) {
                foreach ($paths as $path) {
                    if (Storage::disk('public')->exists($path)) {
                        $mailAttachments[] = Attachment::fromPath(Storage::disk('public')->path($path));
                    }
                }
            } else {
                // Backward compatibility for single file
                if (Storage::disk('public')->exists($this->broadcast->attachment)) {
                    $mailAttachments[] = Attachment::fromPath(Storage::disk('public')->path($this->broadcast->attachment));
                }
            }
        }
        return $mailAttachments;
    }
}
