<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'trainer_id',
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
        'user_id',
        'rejection_reason',
        'approved_at',
        'rejected_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'discount_start' => 'datetime',
        'discount_end' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
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

    /**
     * Approver relation (admin who approved/rejected)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
