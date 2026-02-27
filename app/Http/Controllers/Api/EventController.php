<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Resources\EventResource;
use App\Http\Resources\EventRegistrationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // 1b. Cek apakah event sudah selesai
        if (method_exists($event, 'isFinished') && $event->isFinished()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event sudah selesai, pendaftaran ditutup.'
            ], 422);
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
        
        // Buat Nomor Order Unik (Must be unique for each registration attempt)
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


            // 5. JIKA BERBAYAR -> Arahkan ke Manual Payment
            // Tidak perlu panggil Midtrans. User akan upload bukti bayar nanti.
           
            // 6. Track in Finance (ManualPayment Trace)
            \App\Models\ManualPayment::create([
                'event_id' => $event->id,
                'event_registration_id' => $registration->id,
                'user_id' => $user->id,
                'order_id' => $orderId,
                'amount' => $amount,
                'currency' => 'IDR',
                'method' => $isFree ? 'free' : 'manual_transfer',
                'status' => $isFree ? 'settled' : 'pending',
                'metadata' => ['source' => 'event', 'type' => $isFree ? 'free' : 'paid']
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $isFree ? 'Pendaftaran Berhasil!' : 'Pendaftaran berhasil. Silakan lakukan pembayaran manual dan upload bukti bayar.',
                'data' => [
                    'registration' => $registration,
                    'payment_url' => null // Tidak ada link otomatis
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
    
    /**
     * Cek status pendaftaran event untuk user saat ini.
     */
    public function registrationStatus(Request $request, $id)
    {
        $user = $request->user();

        $event = Event::find($id);
        if (!$event) {
            return response()->json(['status' => 'error', 'message' => 'Event tidak ditemukan'], 404);
        }

        // Tidak bisa membuat pembayaran jika event sudah selesai
        if (method_exists($event, 'isFinished') && $event->isFinished()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event sudah selesai, pembayaran tidak tersedia.'
            ], 422);
        }

        $registration = \App\Models\EventRegistration::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->latest()
            ->first();

        if (!$registration) {
            return response()->json([
                'status' => 'error',
                'message' => 'Belum terdaftar di event ini'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Status pendaftaran',
            'data' => new EventRegistrationResource($registration->load('event')),
        ]);
    }

    /**
     * Daftar pendaftaran event milik user.
     */
    public function listRegistrations(Request $request)
    {
        $user = $request->user();

        $registrations = \App\Models\EventRegistration::with('event')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar pendaftaran event milik user',
            'data' => EventRegistrationResource::collection($registrations),
        ]);
    }

    /**
     * Buat/refresh pendaftaran pending untuk pembayaran manual.
     */
    public function createPayment(Request $request, $id)
    {
        $user = $request->user();
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['status' => 'error', 'message' => 'Event tidak ditemukan'], 404);
        }

        $registration = \App\Models\EventRegistration::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->latest()
            ->first();

        if (!$registration) {
            return response()->json([
                'status' => 'error',
                'message' => 'Belum mendaftar pada event ini'
            ], 409);
        }

        if ($registration->status === 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Pendaftaran sudah LUNAS'
            ], 409);
        }

        // Gunakan total_price yang sudah tercatat di pendaftaran
        $amount = (int) max(0, (int) $registration->total_price);

        if ($amount <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event ini gratis, tidak memerlukan pembayaran'
            ], 400);
        }

        // Buat order id baru agar unik (Update registration_code in DB)
        $orderId = 'REG-' . $user->id . '-' . $event->id . '-' . time();
        $registration->update([
            'registration_code' => $orderId
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Silakan lakukan pembayaran manual dan upload bukti bayar.',
            'data' => new EventRegistrationResource($registration->load('event')),
        ]);
    }

    /**
     * Batalkan pendaftaran pending.
     */
    public function cancelRegistration(Request $request, $id)
    {
        $user = $request->user();
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['status' => 'error', 'message' => 'Event tidak ditemukan'], 404);
        }

        $registration = \App\Models\EventRegistration::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->latest()
            ->first();

        if (!$registration) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pendaftaran tidak ditemukan'
            ], 404);
        }

        if ($registration->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya pendaftaran dengan status pending yang dapat dibatalkan'
            ], 409);
        }

        $registration->update([
            'status' => 'canceled',
            'payment_url' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran berhasil dibatalkan',
            'data' => $registration,
        ]);
    }
    }