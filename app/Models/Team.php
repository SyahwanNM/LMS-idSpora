<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'code',
        'leader_id',
        'status',
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }
}
