<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventExpense extends Model
{
    protected $fillable = [
        'event_id','item','quantity','unit_price','total'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
