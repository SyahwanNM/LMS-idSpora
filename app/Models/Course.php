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
        'free_access_mode',
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

    /**
     * Enrollments relation (students enrolled to this course)
     */
    public function enrollments()
    {
        return $this->hasMany(\App\Models\Enrollment::class);
    }

    /**
     * Payments relation (payments made for this course)
     */


    /**
     * Manual payments relation (QRIS proof uploads)
     */
    public function manualPayments()
    {
        return $this->hasMany(\App\Models\ManualPayment::class);
    }
}
