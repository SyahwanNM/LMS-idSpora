<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentProof extends Model
{
    protected $fillable = [
        'manual_payment_id',
        'event_registration_id',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public function manualPayment()
    {
        return $this->belongsTo(ManualPayment::class);
    }

    public function registration()
    {
        return $this->belongsTo(EventRegistration::class, 'event_registration_id');
    }
}
