<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','event_id','course_id','order_id','snap_token','snap_redirect_url','transaction_id','payment_type','bank','va_number','amount','status','fraud_status','pdf_url','raw_notification'
    ];

    protected $casts = [
        'raw_notification' => 'array',
    ];

    public function user(){ return $this->belongsTo(User::class); }
    public function event(){ return $this->belongsTo(Event::class); }
    public function course(){ return $this->belongsTo(Course::class); }
}
