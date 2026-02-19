<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'enrolled_at',
        'completed_at',
        'enrollment_code',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that the enrollment belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the progress records for this enrollment.
     */
    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    /**
     * Calculate the progress percentage of the course.
     */
    public function getProgressPercentage(): int
    {
        if (!$this->relationLoaded('course') || !$this->course) {
            return 0;
        }

        $totalModules = $this->course->relationLoaded('modules')
            ? $this->course->modules->count()
            : $this->course->modules()->count();
        if ($totalModules === 0) {
            return 0;
        }

        $completedModules = $this->relationLoaded('progress')
            ? $this->progress->where('completed', true)->count()
            : $this->progress()->where('completed', true)->count();
        
        return (int) round(($completedModules / $totalModules) * 100);
    }
}

