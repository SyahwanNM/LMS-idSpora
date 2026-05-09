<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\ManualPayment;
use App\Models\PaymentProof;
use Illuminate\Support\Facades\Storage;

class ManualPaymentController extends Controller
{
    public function register(Request $request, Event $event)
    {
        $user = $request->user();
        if(!$user){ return redirect()->back()->with('error','Harap login terlebih dahulu.'); }

        // If event is free, redirect to normal register
        // Resolve price based on attendance_type for hybrid events
        $attendanceType = strtolower(trim((string) $request->input('attendance_type', 'offline')));
        $isHybridEvent  = !empty($event->maps_url) && !empty($event->zoom_link)
                          && ($event->price_offline > 0 || $event->price_online > 0);

        if ($isHybridEvent) {
            $rawPrice    = $attendanceType === 'online'
                           ? (float) ($event->price_online ?? 0)
                           : (float) ($event->price_offline ?? 0);
            $discountPct = (method_exists($event, 'hasDiscount') && $event->hasDiscount())
                           ? (float) ($event->discount_percentage ?? 0) : 0.0;
            $finalPrice  = $discountPct > 0
                           ? round($rawPrice * (1 - $discountPct / 100), 2)
                           : $rawPrice;
        } else {
            $finalPrice = method_exists($event,'hasDiscount') && $event->hasDiscount()
                          ? ($event->discounted_price ?? $event->price)
                          : ($event->price ?? 0);
        }

        $isFree = (int)$finalPrice <= 0;
        // If free, call existing register endpoint behavior
        if($isFree){
            return app(\App\Http\Controllers\Admin\EventController::class)->register($request, $event);
        }

        // Validate upload
        $request->validate([ 'payment_proof' => 'nullable|image|mimes:jpg,jpeg,png|max:5120' ]);

        // Create or update pending registration
        $existing = EventRegistration::where('user_id', $user->id)->where('event_id', $event->id)->first();

        if (!$existing) {
            $existing = EventRegistration::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'status' => 'pending',
                'registration_code' => 'EVT-'.strtoupper(uniqid()),
                'total_price' => $finalPrice,
            ]);
            $msg = 'Pendaftaran terkirim; menunggu verifikasi admin.';
        } else {
            $existing->update([
                'status' => 'pending',
                'total_price' => $finalPrice,
            ]);
            $msg = 'Bukti pembayaran diperbarui; menunggu verifikasi admin.';
        }

        // Find existing pending or rejected manual payment
        $manual = ManualPayment::where('event_registration_id', $existing->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->orderBy('id', 'desc')
            ->first();

        if (!$manual) {
            $manual = new ManualPayment();
            $manual->order_id = 'MP-' . strtoupper(uniqid());
        }

        $manual->fill([
            'event_id' => $event->id,
            'event_registration_id' => $existing->id,
            'user_id' => $user->id,
            'amount' => $finalPrice,
            'currency' => 'IDR',
            'method' => 'qris',
            'status' => 'pending',
            'referral_code' => $request->input('referral_code'),
            'metadata' => array_merge((array) ($manual->metadata ?? []), [
                'attendance_type' => $attendanceType,
                'source' => 'event',
            ]),
        ])->save();

        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $path = $file->store('payments', 'public');

            // store proof record
            PaymentProof::create([
                'manual_payment_id' => $manual->id,
                'event_registration_id' => $existing->id,
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $user->id,
            ]);

            // keep legacy field for admin UI convenience
            $existing->payment_proof = $path;
            $existing->save();
        }

        return redirect()->route('events.show', $event->id)->with('success', $msg);
    }
}
