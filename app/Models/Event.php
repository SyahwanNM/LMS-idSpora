<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'image',
        'speaker',
        'description',
        'location',
        'price',
        'event_time',
        'event_date',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i',
        'price' => 'decimal:2',
    ];
}
