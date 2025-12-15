<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $name;
    public int $expiresMinutes;

    public function __construct(string $code, string $name, int $expiresMinutes = 15)
    {
        $this->code = $code;
        $this->name = $name;
        $this->expiresMinutes = $expiresMinutes;
    }

    public function build()
    {
        return $this->subject('Kode Verifikasi Pendaftaran - LMS IdSPora')
            ->view('emails.registration-verification')
            ->with([
                'code' => $this->code,
                'name' => $this->name,
                'expires' => $this->expiresMinutes,
            ]);
    }
}
