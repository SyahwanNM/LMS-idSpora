<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventPaymentRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $eventTitle;
    public string $reason;
    public string $messageText;
    public string $adminContactNumber;

    public function __construct(
        string $userName,
        string $eventTitle,
        string $reason,
        string $messageText,
        string $adminContactNumber = '+62 898-9260-731'
    ) {
        $this->userName = $userName;
        $this->eventTitle = $eventTitle;
        $this->reason = $reason;
        $this->messageText = $messageText;
        $this->adminContactNumber = $adminContactNumber;
    }

    public function build()
    {
        return $this->subject('Pembayaran Ditolak - ' . $this->eventTitle)
            ->view('emails.event-payment-rejected')
            ->with([
                'userName' => $this->userName,
                'eventTitle' => $this->eventTitle,
                'reason' => $this->reason,
                'messageText' => $this->messageText,
                'adminContactNumber' => $this->adminContactNumber,
            ]);
    }
}
