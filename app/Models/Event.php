<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'description',
        'location',
        'start_date',
        'end_date',
    ];

    // Relationship to certificates
    public function certificates()
    {
        return $this->morphMany(Certificates::class, 'certifiable');
    }
}
