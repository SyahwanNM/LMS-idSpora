<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTemplateModule extends Model
{
    protected $fillable = [
        'course_template_id',
        'order_no',
        'title',
        'description',
        'type',
        'is_required',
        'duration',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'order_no' => 'integer',
        'duration' => 'integer',
    ];

    public function template()
    {
        return $this->belongsTo(CourseTemplate::class, 'course_template_id');
    }
}
