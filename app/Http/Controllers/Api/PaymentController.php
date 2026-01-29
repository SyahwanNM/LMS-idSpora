<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManualPayment;
use App\Models\PaymentProof;
use App\Models\EventRegistration;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * List user's manual payments.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $payments = ManualPayment::with(['event', 'registration'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar pembayaran manual',
            'data' => $payments,
        ]);
    }

    /**
     * Show details of a manual payment.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $payment = ManualPayment::with(['event', 'registration', 'proofs'])
            ->where('user_id', $user->id)
            ->find($id);

        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => 'Pembayaran tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail pembayaran',
            'data' => $payment,
        ]);
    }

    /**
     * Submit manual payment (create or update proof).
     * Expects: event_id, payment_proof (file)
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user = $request->user();
        $eventId = $request->input('event_id');
        $event = Event::find($eventId);

        // Find registration
        $registration = EventRegistration::where('user_id', $user->id)
            ->where('event_id', $eventId)
            ->first();

        if (!$registration) {
            return response()->json(['status' => 'error', 'message' => 'Anda belum terdaftar di event ini.'], 404);
        }

        // Determine amount
        $amount = (int) $registration->total_price;
        if ($amount <= 0) {
             return response()->json(['status' => 'error', 'message' => 'Event ini gratis, tidak perlu pembayaran.'], 400);
        }

        DB::beginTransaction();
        try {
            // Find or Create ManualPayment Record
            $manualPayment = ManualPayment::where('event_registration_id', $registration->id)->first();

            if (!$manualPayment) {
                $manualPayment = ManualPayment::create([
                    'event_id' => $event->id,
                    'event_registration_id' => $registration->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'currency' => 'IDR',
                    'method' => 'manual_transfer', // Default method
                    'status' => 'pending',
                ]);
            } else {
                 // Reset status if re-uploading
                 $manualPayment->update(['status' => 'pending']);
            }

            // Handle File Upload
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $path = $file->store('payments', 'public');

                // Create Proof Record
                PaymentProof::create([
                    'manual_payment_id' => $manualPayment->id,
                    'event_registration_id' => $registration->id,
                    'file_path' => $path,
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => $user->id,
                ]);

                // Update legacy field
                $registration->update([
                    'status' => 'pending', // Ensure status is pending waiting for admin
                    'payment_proof' => $path
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Bukti pembayaran berhasil diupload. Mohon tunggu verifikasi admin.',
                'data' => $manualPayment->load('proofs'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update proof for an existing payment (using POST because of file upload).
     */
    public function update(Request $request, $id)
    {
        // Effectively same as store but targeting specific payment ID
        // For simplicity, let's allow users to just use store endpoint generally, 
        // OR implements strict update here. 
        
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user = $request->user();
        $manualPayment = ManualPayment::where('user_id', $user->id)->find($id);

        if (!$manualPayment) {
            return response()->json(['status' => 'error', 'message' => 'Pembayaran tidak ditemukan'], 404);
        }

        DB::beginTransaction();
        try {
            $file = $request->file('payment_proof');
            $path = $file->store('payments', 'public');

            PaymentProof::create([
                'manual_payment_id' => $manualPayment->id,
                'event_registration_id' => $manualPayment->event_registration_id,
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $user->id,
            ]);

            $manualPayment->update(['status' => 'pending']);
            
             // Create proof record
            $registration = EventRegistration::find($manualPayment->event_registration_id);
            if($registration){
                $registration->update(['status' => 'pending', 'payment_proof' => $path]);
            }

            DB::commit();

             return response()->json([
                'status' => 'success',
                'message' => 'Bukti pembayaran berhasil diperbarui.',
                'data' => $manualPayment->load('proofs'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal update pembayaran: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Cancel manual payment.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $manualPayment = ManualPayment::where('user_id', $user->id)->find($id);

        if (!$manualPayment) {
            return response()->json(['status' => 'error', 'message' => 'Pembayaran tidak ditemukan'], 404);
        }

        if ($manualPayment->status == 'paid' || $manualPayment->status == 'verified') {
             return response()->json(['status' => 'error', 'message' => 'Pembayaran yang sudah diverifikasi tidak dapat dibatalkan.'], 400);
        }

        DB::beginTransaction();
        try {
            $manualPayment->update(['status' => 'cancelled']); // or 'canceled' check enum consistency
            
            $registration = EventRegistration::find($manualPayment->event_registration_id);
            if($registration){
                $registration->update(['status' => 'canceled']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran dibatalkan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
             return response()->json(['status' => 'error', 'message' => 'Gagal membatalkan: ' . $e->getMessage()], 500);
        }
    }
}
