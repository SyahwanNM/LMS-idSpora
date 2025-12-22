<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'status',
        'attendance_status',
        'attended_at',
        'registration_code',
        'certificate_number',
        'certificate_issued_at',
        'feedback_text',
        'feedback_submitted_at',
        'total_price', 
        'payment_url',
    ];

    protected $casts = [
        'certificate_issued_at' => 'datetime',
        'feedback_submitted_at' => 'datetime',
        'attended_at' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function event(){
        return $this->belongsTo(Event::class);
    }
}
