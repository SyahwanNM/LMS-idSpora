<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseUnit extends Model
{
    protected $fillable = [
        'course_id',
        'unit_no',
        'title',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
