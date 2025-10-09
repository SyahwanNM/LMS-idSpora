<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $fillable = [
        'user_id', 'event_id', 'status', 'registration_code', 'certificate_number', 'certificate_issued_at'
    ];

    protected $casts = [
        'certificate_issued_at' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function event(){
        return $this->belongsTo(Event::class);
    }
}
