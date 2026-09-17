<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistrationWinner extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'event_registration_id',
        'event_winner_category_id',
        'winner_title',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registration()
    {
        return $this->belongsTo(EventRegistration::class, 'event_registration_id');
    }

    public function category()
    {
        return $this->belongsTo(EventWinnerCategory::class, 'event_winner_category_id');
    }
}
