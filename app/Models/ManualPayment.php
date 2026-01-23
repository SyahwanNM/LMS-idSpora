<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualPayment extends Model
{
    protected $fillable = [
        'event_id',
        'event_registration_id',
        'user_id',
        'order_id',
        'amount',
        'currency',
        'method',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registration()
    {
        return $this->belongsTo(EventRegistration::class, 'event_registration_id');
    }

    public function proofs()
    {
        return $this->hasMany(PaymentProof::class);
    }
}
