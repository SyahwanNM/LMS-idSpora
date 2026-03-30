<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\ManualPayment;

class EventRegistration extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'status',
        'attendance_status',
        'attended_at',
        'attendance_scan_qr',
        'registration_code',
        'certificate_number',
        'certificate_issued_at',
        'feedback_text',
        'feedback_submitted_at',
        'total_price', 
        'payment_proof',
        'payment_verified_at',
        'payment_verified_by',
        'payment_url',
        'rejection_reason',
    ];

    protected $casts = [
        'certificate_issued_at' => 'datetime',
        'feedback_submitted_at' => 'datetime',
        'attended_at' => 'datetime',
        'attendance_scan_qr' => 'datetime',
    ];

    public function getInvoiceUrlAttribute()
    {
        // Check Manual
        $manual = ManualPayment::where('event_id', $this->event_id)
            ->where('user_id', $this->user_id)
            ->where('status', 'settled')
            ->first();

        if ($manual) {
            return $manual->order_id ? route('invoice.manual', $manual->order_id) : null;
        }

        return null;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function event(){
        return $this->belongsTo(Event::class);
    }
}
