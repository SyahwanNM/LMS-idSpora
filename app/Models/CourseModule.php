<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseModule extends Model
{
    use HasFactory;

    protected $table = 'course_module';

    protected $fillable = [
        'course_id',
        'order_no',
        'title',
        'description',
        'type',
        'content_url',
        'is_free',
        'preview_pages',
        'duration',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'order_no' => 'integer',
        'preview_pages' => 'integer',
        'duration' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order_no');
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        
        return $minutes . 'm';
    }

    public function getFileExtensionAttribute()
    {
        return pathinfo($this->content_url, PATHINFO_EXTENSION);
    }

    public function isVideo()
    {
        return $this->type === 'video';
    }

    public function isPdf()
    {
        return $this->type === 'pdf';
    }

    public function isQuiz()
    {
        return $this->type === 'quiz';
    }
}