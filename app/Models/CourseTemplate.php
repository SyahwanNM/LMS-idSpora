<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTemplate extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'level',
        'version',
        'status',
        'created_by',
        'description',
    ];

    protected $casts = [
        'version' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modules()
    {
        return $this->hasMany(CourseTemplateModule::class)->orderBy('order_no');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'template_id');
    }
}
