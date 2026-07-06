<?php

namespace App\Mail;

use App\Models\SupportMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
<<<<<<< HEAD
=======
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
>>>>>>> b863fb54e2abec006fb54479f68889751e33734a
use Illuminate\Queue\SerializesModels;

class SupportTicketStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public SupportMessage $ticket;
    public string $statusLabel;
    public string $messageText;

    /**
     * Create a new message instance.
     */
    public function __construct(SupportMessage $ticket)
    {
        $this->ticket = $ticket;
<<<<<<< HEAD
        
=======

>>>>>>> b863fb54e2abec006fb54479f68889751e33734a
        if ($ticket->status === 'resolved') {
            $this->statusLabel = 'Selesai';
            $this->messageText = 'Keluhan / kendala yang Anda kirimkan telah berhasil diselesaikan oleh tim kami. Terima kasih atas kerja sama dan laporan Anda.';
        } else {
            $this->statusLabel = 'Ditolak / Diabaikan';
            $this->messageText = 'Setelah dilakukan peninjauan, keluhan / laporan yang Anda kirimkan tidak dapat kami proses lebih lanjut (diabaikan atau ditolak oleh admin).';
        }
    }

    /**
<<<<<<< HEAD
     * Build the message.
     */
    public function build()
    {
        $subjectText = '[' . $this->statusLabel . '] Tiket Dukungan #' . str_pad($this->ticket->id, 5, '0', STR_PAD_LEFT) . ' - ' . $this->ticket->subject;

        return $this->subject($subjectText)
            ->view('emails.support-ticket-status')
            ->with([
                'ticket' => $this->ticket,
                'statusLabel' => $this->statusLabel,
                'messageText' => $this->messageText,
            ]);
=======
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjectText = '[' . $this->statusLabel . '] Tiket Dukungan #'
            . str_pad($this->ticket->id, 5, '0', STR_PAD_LEFT)
            . ' - ' . $this->ticket->subject;

        return new Envelope(
            subject: $subjectText,
            from: new \Illuminate\Mail\Mailables\Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.support-ticket-status',
            with: [
                'ticket'      => $this->ticket,
                'statusLabel' => $this->statusLabel,
                'messageText' => $this->messageText,
            ],
        );
>>>>>>> b863fb54e2abec006fb54479f68889751e33734a
    }
}
