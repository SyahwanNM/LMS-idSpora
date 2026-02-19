<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningTimeDaily extends Model
{
    protected $table = 'learning_time_dailies';

    protected $fillable = [
        'user_id',
        'course_id',
        'learned_on',
        'seconds',
    ];

    protected $casts = [
        'learned_on' => 'date',
        'seconds' => 'integer',
    ];
}
