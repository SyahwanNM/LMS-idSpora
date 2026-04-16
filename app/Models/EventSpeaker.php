<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventSpeaker extends Model
{
    protected $fillable = [
        'event_id',
        'trainer_id',
        'name',
        'salary',
        'notes',
        'order',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'order'  => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
