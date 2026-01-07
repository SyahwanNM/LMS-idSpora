<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventRegistration;
use Midtrans\Config;

class CallbackController extends Controller
{
    public function callback(Request $request)
    {
        // Konfigurasi (gunakan config untuk konsistensi)
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.sanitize');
        Config::$is3ds = (bool) config('midtrans.enable_3ds');

        try {
            // BYPASS VALIDASI (Ambil data dari Postman)
            $notif = (object) $request->all(); 

            $transaction = $notif->transaction_status ?? null;
            $type = $notif->payment_type ?? null;
            $orderId = $notif->order_id ?? null; 
            $fraud = $notif->fraud_status ?? null;

            // Cari Data
            $registration = EventRegistration::where('registration_code', $orderId)->first();

            if (!$registration) {
                return response()->json(['message' => 'Order ID not found in database'], 404);
            }

            // Update Status
            if ($transaction == 'settlement') {
                $registration->update(['status' => 'active']);
            } else if ($transaction == 'pending') {
                $registration->update(['status' => 'pending']);
            } else if ($transaction == 'expire' || $transaction == 'cancel' || $transaction == 'deny') {
                $registration->update(['status' => 'cancelled']);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Notification processed via Testing Mode',
                'updated_status' => $registration->status
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}d