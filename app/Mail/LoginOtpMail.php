<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $name;
    public int $minutes;

    public function __construct(string $code, string $name, int $minutes = 10)
    {
        $this->code = $code;
        $this->name = $name;
        $this->minutes = $minutes;
    }

    public function build()
    {
        return $this->subject('Kode OTP Login Anda')
            ->view('emails.login-otp')
            ->with([
                'code' => $this->code,
                'name' => $this->name,
                'minutes' => $this->minutes,
            ]);
    }
}
