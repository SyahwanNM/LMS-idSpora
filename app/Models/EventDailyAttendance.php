<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventDailyAttendance extends Model
{
    protected $fillable = [
        'event_registration_id',
        'event_daily_qr_id',
        'attendance_date',
        'day_number',
        'scanned_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'scanned_at'      => 'datetime',
    ];

    public function registration()
    {
        return $this->belongsTo(EventRegistration::class, 'event_registration_id');
    }

    public function dailyQr()
    {
        return $this->belongsTo(EventDailyQr::class, 'event_daily_qr_id');
    }
}
