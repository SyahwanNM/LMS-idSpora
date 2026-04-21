<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quizzez';

    protected $fillable = [
        'course_id',
        'course_module_id',
        'title',
        'description',
        'passing_grade',
        'quiz_type',
        'bagian_order_no',
        'duration_minutes',
        'num_questions',
        'is_active',
        'total_questions',
        'pass_score',
    ];

    protected $casts = [
        'passing_grade' => 'integer',
        'total_questions' => 'integer',
        'pass_score' => 'integer',
        'bagian_order_no' => 'integer',
        'duration_minutes' => 'integer',
        'num_questions' => 'integer',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function module()
    {
        return $this->belongsTo(\App\Models\CourseModule::class, 'course_module_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order_no');
    }

    /**
     * Check if this is a section quiz
     */
    public function isSectionQuiz(): bool
    {
        return $this->quiz_type === 'section_quiz';
    }

    /**
     * Check if this is final quiz
     */
    public function isFinalQuiz(): bool
    {
        return $this->quiz_type === 'final_quiz';
    }
}
