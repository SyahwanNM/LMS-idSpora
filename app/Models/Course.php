<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'instructor',
        'duration',
        'level',
    ];

    // Relationship to certificates
    public function certificates()
    {
        return $this->morphMany(Certificates::class, 'certifiable');
    }
}
