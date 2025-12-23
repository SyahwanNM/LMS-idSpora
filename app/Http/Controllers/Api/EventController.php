<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Midtrans\Config;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::active()->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'List Event Terbaru',
            'data' => EventResource::collection($events), 
        ]);
    }

    public function show($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['status' => 'error', 'message' => 'Event tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail Event',
            'data' => new EventResource($event),
        ]);
    }
    
   public function register(Request $request, $id)
    {
        $user = $request->user();
        $event = Event::find($id);

        // 1. Validasi Event
        if (!$event) {
            return response()->json(['status' => 'error', 'message' => 'Event tidak ditemukan'], 404);
        }

        // 2. Cek Apakah User Sudah Terdaftar
        $existing = \App\Models\EventRegistration::where('user_id', $user->id)
                    ->where('event_id', $event->id)
                    ->first();

        if ($existing) {
            // Kalau sudah daftar tapi belum bayar, kasih link bayar yang lama
            if ($existing->status == 'pending' && $existing->payment_url) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Anda sudah mendaftar, silakan selesaikan pembayaran.',
                    'data' => $existing
                ], 200);
            }
            
            return response()->json([
                'status' => 'error',
                'message' => 'Kamu sudah terdaftar di event ini!',
                'data' => $existing
            ], 409);
        }

        // 3. Hitung Harga
        $isFree = $event->discounted_price <= 0;
        $amount = $isFree ? 0 : $event->discounted_price;
        
        // Buat Nomor Order Unik (PENTING: Midtrans menolak order_id yang sama persis)
        // Format: REG-{UserID}-{EventID}-{Timestamp}
        $orderId = 'REG-' . $user->id . '-' . $event->id . '-' . time();

        try {
            DB::beginTransaction();

            // 4. Simpan ke Database (Status Pending)
            $registration = \App\Models\EventRegistration::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'status' => $isFree ? 'active' : 'pending',
                'registration_code' => $orderId,
                'total_price' => $amount,
                'payment_url' => null, // Nanti diisi
            ]);

            $paymentUrl = null;

            // 5. JIKA BERBAYAR -> Panggil Midtrans
            if (!$isFree) {
                // Setup Konfigurasi Midtrans
                Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
                Config::$isSanitized = true;
                Config::$is3ds = true;

                // Siapkan Data Transaksi
                $params = [
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => (int) $amount,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email' => $user->email,
                    ],
                    'item_details' => [
                        [
                            'id' => $event->id,
                            'price' => (int) $amount,
                            'quantity' => 1,
                            'name' => substr($event->title, 0, 49), // Midtrans max 50 huruf
                        ]
                    ]
                ];

                // Minta Link Pembayaran ke Midtrans
                $paymentUrl = Snap::createTransaction($params)->redirect_url;
                
                // Update Database dengan Link baru
                $registration->update(['payment_url' => $paymentUrl]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $isFree ? 'Pendaftaran Berhasil!' : 'Silakan lakukan pembayaran melalui link di bawah.',
                'data' => [
                    'registration' => $registration,
                    'payment_url' => $paymentUrl 
                ]
            ], 201);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Gagal memproses pendaftaran: ' . $e->getMessage()
                ], 500);
            }
        }
    }