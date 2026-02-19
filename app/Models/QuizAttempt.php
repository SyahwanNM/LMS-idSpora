<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_module_id',
        'score',
        'total_questions',
        'correct_answers',
        'answers',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'answers' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courseModule()
    {
        return $this->belongsTo(CourseModule::class);
    }

    public function getPercentageAttribute()
    {
        if ($this->total_questions == 0) {
            return 0;
        }
        return round(($this->correct_answers / $this->total_questions) * 100, 2);
    }

    public function getGradeAttribute()
    {
        $percentage = $this->percentage;
        
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    public function isPassed($passingScore = 75)
    {
        return $this->percentage >= $passingScore;
    }
}