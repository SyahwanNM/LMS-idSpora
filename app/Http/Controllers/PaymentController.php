<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Transaction;
use Midtrans\CoreApi;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Payment;
use App\Models\UserNotification;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{
    protected function configureMidtrans(): void
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = (bool) config('midtrans.is_production');
        MidtransConfig::$isSanitized = (bool) config('midtrans.sanitize');
        MidtransConfig::$is3ds = (bool) config('midtrans.enable_3ds');
    }

    public function snapToken(Request $request, Event $event)
    {
        $this->configureMidtrans();

        $user = Auth::user();
        // Accept phone/name/email overrides from request (e.g., WhatsApp number from payment form)
        $reqPhone = trim((string) $request->input('phone', ''));
        $reqName = trim((string) $request->input('name', ''));
        $reqEmail = trim((string) $request->input('email', ''));
        // Sanitize phone to allow only + and digits
        if ($reqPhone !== '') {
            $reqPhone = preg_replace('/[^0-9\+]/', '', $reqPhone) ?? '';
        }
        // Determine price (respect discount if exists)
        $price = method_exists($event, 'hasDiscount') && $event->hasDiscount()
            ? ($event->discounted_price ?? $event->price)
            : ($event->price ?? 0);

        $orderId = 'EVT-' . $event->id . '-' . now()->format('YmdHis') . '-' . Str::random(5);

        // Ensure valid gross amount for QRIS & others (use IDR >= 1000 for reliability in sandbox)
        $amountInt = max(1000, (int) round((float) $price));

        // If final price is 0 treat as free, don't call Midtrans
        if ((int)$price <= 0) {
            return response()->json([
                'free' => true,
                'orderId' => $orderId,
            ], 200);
        }

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amountInt,
                'currency' => 'IDR',
            ],
            'item_details' => [[
                'id' => 'event-'.$event->id,
                'price' => $amountInt,
                'quantity' => 1,
                'name' => Str::limit($event->title, 50),
            ]],
            'customer_details' => [
                'first_name' => $reqName !== '' ? $reqName : ($user?->name ?? 'Guest'),
                'email' => $reqEmail !== '' ? $reqEmail : ($user?->email ?? 'guest@example.com'),
                // Use WhatsApp number from form if provided
                'phone' => $reqPhone !== '' ? $reqPhone : null,
            ],
            // Force-enable QRIS and commonly used channels for easier testing
            'enabled_payments' => ['qris','bank_transfer','gopay','echannel','permata'],
            // Keep QR valid long enough to scan on simulator
            'expiry' => [
                'unit' => 'minute',
                'duration' => 15,
            ],
            'callbacks' => [
                // Optional redirect callback after payment
                'finish' => url('/payment/finish'),
            ],
        ];
        // Remove nulls from customer_details to avoid Midtrans validation issues
        $params['customer_details'] = array_filter($params['customer_details'], function($v){ return !is_null($v) && $v !== ''; });

        // Create payment row in DB (pending)
        Payment::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'order_id' => $orderId,
            'gross_amount' => $amountInt,
            'status' => 'pending',
        ]);

        // Remember order id in session for later finalize check
        session(["midtrans_order_{$event->id}" => $orderId]);

        // For free events, we could skip payment and directly mark as registered; let frontend handle free case
        try {
            \Log::info('[Midtrans][snapToken] create', [
                'order_id' => $orderId,
                'amount' => $amountInt,
                'enabled_payments' => $params['enabled_payments'] ?? null,
            ]);
            $snapToken = Snap::getSnapToken($params);
            return response()->json([
                'snapToken' => $snapToken,
                'clientKey' => config('midtrans.client_key'),
                // Return both camelCase and snake_case for compatibility with existing frontends
                'orderId' => $orderId,
                'order_id' => $orderId,
            ]);
        } catch (\Throwable $e) {
            \Log::error('[Midtrans][snapToken] error', [ 'order_id' => $orderId, 'error' => $e->getMessage() ]);
            return response()->json([
                'error' => 'midtrans_error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function notify(Request $request)
    {
        // Handle Midtrans HTTP Notification
        $payload = $request->all();
        \Log::info('[Midtrans][notify] incoming', [
            'order_id' => $payload['order_id'] ?? null,
            'transaction_status' => $payload['transaction_status'] ?? null,
            'status_code' => $payload['status_code'] ?? null,
            'payment_type' => $payload['payment_type'] ?? null,
        ]);
        $orderId = $payload['order_id'] ?? null;
        if(!$orderId){ return response()->json(['message' => 'invalid'], 422); }

        // Optional: verify signature key
        $signatureKey = $payload['signature_key'] ?? null;
        $serverKey = config('midtrans.server_key');
        $check = hash('sha512', ($orderId . ($payload['status_code'] ?? '') . ($payload['gross_amount'] ?? '') . $serverKey));
        if(!$signatureKey || !hash_equals($check, $signatureKey)){
            \Log::warning('[Midtrans][notify] invalid signature', [
                'order_id' => $orderId,
                'calc' => $check,
                'provided' => $signatureKey,
            ]);
            return response()->json(['message' => 'invalid signature'], 401);
        }

        $payment = Payment::where('order_id', $orderId)->first();
        if(!$payment){ return response()->json(['message' => 'order not found'], 404); }

        $va = $payload['va_numbers'][0] ?? null;
        $payment->update([
            'transaction_id' => $payload['transaction_id'] ?? $payment->transaction_id,
            'payment_type' => $payload['payment_type'] ?? $payment->payment_type,
            'bank' => $payload['bank'] ?? ($va['bank'] ?? $payment->bank),
            'va_number' => $va['va_number'] ?? $payment->va_number,
            'status' => $payload['transaction_status'] ?? $payment->status,
            'fraud_status' => $payload['fraud_status'] ?? $payment->fraud_status,
            'pdf_url' => $payload['pdf_url'] ?? $payment->pdf_url,
            'raw_notification' => $payload,
        ]);

        // If settlement (paid), auto register the user to the event
        if(in_array($payment->status, ['capture','settlement'])){
            $exists = EventRegistration::where('user_id',$payment->user_id)->where('event_id',$payment->event_id)->exists();
            if(!$exists){
                EventRegistration::create([
                    'user_id' => $payment->user_id,
                    'event_id' => $payment->event_id,
                    'status' => 'active',
                    'registration_code' => 'EVT-'.strtoupper(uniqid())
                ]);
                // Notification for paid registration
                try{
                    $ev = Event::find($payment->event_id);
                    if($ev){
                        UserNotification::create([
                            'user_id' => $payment->user_id,
                            'type' => 'event_registration',
                            'title' => 'Pembayaran Berhasil',
                            'message' => 'Pendaftaran untuk "'.$ev->title.'" telah dikonfirmasi.',
                            'data' => ['url' => route('events.show', $ev)],
                            'expires_at' => now()->addDays(14),
                        ]);
                    }
                }catch(\Throwable $e){ /* ignore */ }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function finalize(Request $request, Event $event)
    {
        $this->configureMidtrans();
        $user = $request->user();
        if(!$user){
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        // Optional: verify order id matches last generated for this event
        $orderId = session("midtrans_order_{$event->id}");
        if(!$orderId){
            return response()->json(['message' => 'Order tidak ditemukan'], 422);
        }
        // Try to refresh payment status from Midtrans in case webhook didn't reach us
        $payment = Payment::where('order_id', $orderId)->first();
        $paymentStatus = null;
        try {
            $status = Transaction::status($orderId);
            $payload = json_decode(json_encode($status), true);
            $paymentStatus = $payload['transaction_status'] ?? null;
            if($payment){
                $va = $payload['va_numbers'][0] ?? null;
                $payment->update([
                    'transaction_id' => $payload['transaction_id'] ?? $payment->transaction_id,
                    'payment_type' => $payload['payment_type'] ?? $payment->payment_type,
                    'bank' => $payload['bank'] ?? ($va['bank'] ?? $payment->bank),
                    'va_number' => $va['va_number'] ?? $payment->va_number,
                    'status' => $paymentStatus ?? $payment->status,
                    'fraud_status' => $payload['fraud_status'] ?? $payment->fraud_status,
                    'pdf_url' => $payload['pdf_url'] ?? $payment->pdf_url,
                    'raw_notification' => $payload,
                ]);
            }
        } catch (\Throwable $e) {
            \Log::warning('[Midtrans][finalize] failed to refresh status', ['order_id'=>$orderId,'error'=>$e->getMessage()]);
        }

        $registered = false;
        // Register only when already paid (capture/settlement) OR if event is free
        $finalPrice = (method_exists($event,'hasDiscount') && $event->hasDiscount()) ? ($event->discounted_price ?? $event->price) : ($event->price ?? 0);
        $isFree = (int)$finalPrice <= 0;
        if($isFree || in_array($paymentStatus, ['capture','settlement'])){
            $exists = EventRegistration::where('user_id',$user->id)->where('event_id',$event->id)->exists();
            if(!$exists){
                EventRegistration::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'status' => 'active',
                    'registration_code' => 'EVT-'.strtoupper(uniqid())
                ]);
                try{
                    UserNotification::create([
                        'user_id' => $user->id,
                        'type' => 'event_registration',
                        'title' => $isFree ? 'Pendaftaran Event Berhasil' : 'Pembayaran Berhasil',
                        'message' => ($isFree ? 'Kamu terdaftar di ' : 'Pendaftaran untuk ').'"'.$event->title.'" telah dikonfirmasi.',
                        'data' => ['url' => route('events.show', $event)],
                        'expires_at' => now()->addDays(14),
                    ]);
                }catch(\Throwable $e){ /* ignore */ }
            }
            $registered = true;
        }
        return response()->json([
            'status' => 'ok',
            'payment_status' => $paymentStatus,
            'registered' => $registered,
        ]);
    }

    /**
     * Fallback: Create QRIS via Core API and return QR string + PNG (base64) for manual scan.
     */
    public function qrisCore(Request $request, Event $event)
    {
        $this->configureMidtrans();
        $user = Auth::user();
        if(!$user){
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        // Compute effective price
        $price = (method_exists($event,'hasDiscount') && $event->hasDiscount()) ? ($event->discounted_price ?? $event->price) : ($event->price ?? 0);
        $amountInt = max(1000, (int) round((float) $price));

        $orderId = 'QRC-' . $event->id . '-' . now()->format('YmdHis') . '-' . Str::random(5);

        // Create pending payment row
        Payment::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'order_id' => $orderId,
            'gross_amount' => $amountInt,
            'status' => 'pending',
        ]);
        session(["midtrans_order_{$event->id}" => $orderId]);

        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amountInt,
            ],
            'item_details' => [[
                'id' => 'event-'.$event->id,
                'price' => $amountInt,
                'quantity' => 1,
                'name' => Str::limit($event->title, 50),
            ]],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
        ];

        try{
            \Log::info('[Midtrans][qrisCore] charge', ['order_id'=>$orderId,'amount'=>$amountInt]);
            $res = CoreApi::charge($params);
            $arr = json_decode(json_encode($res), true);
            $qrString = $arr['qr_string'] ?? null;
            $actions = $arr['actions'] ?? [];
            $pngBase64 = null;
            if($qrString){
                try{
                    $png = QrCode::format('png')->size(280)->margin(1)->generate($qrString);
                    $pngBase64 = base64_encode($png);
                }catch(\Throwable $e){ /* QR generation optional */ }
            }
            return response()->json([
                'order_id' => $orderId,
                'qr_string' => $qrString,
                'qr_png' => $pngBase64 ? ('data:image/png;base64,'.$pngBase64) : null,
                'actions' => $actions,
            ]);
        }catch(\Throwable $e){
            \Log::error('[Midtrans][qrisCore] error', ['order_id'=>$orderId,'error'=>$e->getMessage()]);
            return response()->json([
                'error' => 'midtrans_qris_error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
