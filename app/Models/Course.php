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
        'status',
        'price',
        'duration',
        'media',
        'media_type',
        'card_thumbnail',
        'discount_percent',
        'discount_start',
        'discount_end',
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

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
