<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventRegistration;
use Midtrans\Config;
use Midtrans\Notification;

class CallbackController extends Controller
{
    public function callback()
    {
        // 1. Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            // 2. Terima Notifikasi dari Midtrans
            $notif = new Notification();

            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $orderId = $notif->order_id; // Ini adalah registration_code kita
            $fraud = $notif->fraud_status;

            // 3. Cari Data Registrasi berdasarkan Order ID
            $registration = EventRegistration::where('registration_code', $orderId)->first();

            if (!$registration) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            // 4. Logic Update Status
            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $registration->update(['status' => 'pending']);
                    } else {
                        $registration->update(['status' => 'active']); // LUNAS
                    }
                }
            } else if ($transaction == 'settlement') {
                // INI YANG PALING PENTING (Gopay, VA, dll masuk sini kalau sukses)
                $registration->update(['status' => 'active']); // LUNAS
                
            } else if ($transaction == 'pending') {
                $registration->update(['status' => 'pending']);
                
            } else if ($transaction == 'deny') {
                $registration->update(['status' => 'cancelled']);
                
            } else if ($transaction == 'expire') {
                $registration->update(['status' => 'cancelled']);
                
            } else if ($transaction == 'cancel') {
                $registration->update(['status' => 'cancelled']);
            }

            return response()->json(['message' => 'Notification processed'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}