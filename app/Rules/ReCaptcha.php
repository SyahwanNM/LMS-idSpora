<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class ReCaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secretKey = config('services.recaptcha.secret_key');

        if (empty($secretKey)) {
            return;
        }

        try {
            $response = Http::asForm()
                ->withoutVerifying()
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secretKey,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

            if (!$response->successful() || !$response->json('success')) {
                \Log::warning('ReCAPTCHA verification failed: ' . json_encode($response->json() ?? $response->body()));
                $fail('Verifikasi reCAPTCHA gagal. Silakan coba lagi.');
            }
        } catch (\Throwable $e) {
            \Log::error('ReCAPTCHA verification exception: ' . $e->getMessage());
            $fail('Verifikasi reCAPTCHA gagal. Silakan coba lagi.');
        }
    }
}
