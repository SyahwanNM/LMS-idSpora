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

        abort(403, 'Pembayaran manual tidak diaktifkan. Gunakan Midtrans.');
    }
}
