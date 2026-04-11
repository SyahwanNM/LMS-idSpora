<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'template_id',
        'template_version',
        'trainer_id',
        'trainer_contribution_scheme',
        'trainer_revenue_percent',
        'trainer_scheme_accepted_at',
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
        'trainer_scheme_accepted_at' => 'datetime',
        'expenses_json',
    ];

    // protected $casts = [
    //     'expenses_json' => 'array',
    //     'discount_start' => 'date',
    //     'discount_end' => 'date',
    // ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function template()
    {
        return $this->belongsTo(CourseTemplate::class, 'template_id');
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('order_no');
    }

    public function units()
    {
        return $this->hasMany(CourseUnit::class)->orderBy('unit_no');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'course_id');
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
