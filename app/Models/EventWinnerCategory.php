<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventWinnerCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'certificate_template',
        'certificate_custom_template',
        'certificate_logo',
        'certificate_signature',
        'file_tambahan',
        'order',
    ];

    protected $casts = [
        'certificate_custom_template' => 'array',
        'certificate_logo' => 'array',
        'certificate_signature' => 'array',
        'order' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function winnerAssignments()
    {
        return $this->hasMany(EventRegistrationWinner::class, 'event_winner_category_id');
    }

    public function registrations()
    {
        return $this->belongsToMany(EventRegistration::class, 'event_registration_winners', 'event_winner_category_id', 'event_registration_id')
                    ->withPivot('winner_title')
                    ->withTimestamps();
    }
}
