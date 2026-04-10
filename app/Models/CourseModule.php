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
        'file_name',
        'mime_type',
        'file_size',
        'is_free',
        'preview_pages',
        'duration',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'order_no' => 'integer',
        'preview_pages' => 'integer',
        'duration' => 'integer',
        'file_size' => 'integer',
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
        // duration now stored in SECONDS
        $seconds = (int) $this->duration;
        if ($seconds <= 0) {
            return '0 detik';
        }
        $h = intdiv($seconds, 3600);
        $rem = $seconds % 3600;
        $m = intdiv($rem, 60);
        $s = $rem % 60;
        if ($h > 0) {
            return $h.'j '.$m.'m '.$s.'d';
        }
        if ($m > 0) {
            return $m.'m '.$s.'d';
        }
        return $s.' detik';
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