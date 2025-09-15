<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_module_id',
        'question',
        'explanation',
        'order_no',
        'points',
    ];

    protected $casts = [
        'order_no' => 'integer',
        'points' => 'integer',
    ];

    public function courseModule()
    {
        return $this->belongsTo(CourseModule::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class)->orderBy('order_no');
    }

    public function correctAnswer()
    {
        return $this->hasOne(QuizAnswer::class)->where('is_correct', true);
    }

    public function getCorrectAnswerIdAttribute()
    {
        return $this->correctAnswer?->id;
    }
}