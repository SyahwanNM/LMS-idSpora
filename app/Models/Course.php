<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'description',
        'level',
        'price',
        'duration',
        'image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('order_no');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
