<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventScheduleItem extends Model
{
    protected $fillable = [
        'event_id','start','end','title','description'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
