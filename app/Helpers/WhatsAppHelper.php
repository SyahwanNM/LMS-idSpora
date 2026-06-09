<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppHelper
{
    /**
     * Send WhatsApp message via Fonnte API.
     *
     * @param string $target Receiver phone number (e.g. 08123456789 or +628123456789)
     * @param string $message The message content
     * @return bool True if successfully sent, false otherwise
     */
    public static function send($target, $message)
    {
        $token = env('FONNTE_TOKEN');
        if (empty($token)) {
            Log::warning('WhatsApp tidak terkirim: FONNTE_TOKEN belum diatur di .env');
            return false;
        }

        if (empty($target)) {
            Log::warning('WhatsApp tidak terkirim: Nomor target kosong.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('Fonnte WA Send failed. Response: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('Fonnte WA Send Exception: ' . $e->getMessage());
            return false;
        }
    }
}
