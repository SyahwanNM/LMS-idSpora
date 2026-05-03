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
        'certificate_number',
        'certificate_issued_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'certificate_issued_at' => 'datetime',
    ];

    /**
     * Get the user that owns the enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for `user()` to keep backward compatibility with older code
     * that expects an enrollment to have a `student` relationship.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
     * Get the ID of the next module to continue (first incomplete module).
     * Returns null if all modules are completed or no modules exist.
     */
    public function getNextModuleId(): ?int
    {
        if (!$this->relationLoaded('course') || !$this->course) {
            return null;
        }

        $modules = $this->course->relationLoaded('modules')
            ? $this->course->modules->sortBy('order_no')
            : $this->course->modules()->orderBy('order_no')->get();

        if ($modules->isEmpty()) {
            return null;
        }

        $completedIds = $this->relationLoaded('progress')
            ? $this->progress->where('completed', true)->pluck('course_module_id')->map(fn($id) => (int) $id)->all()
            : $this->progress()->where('completed', true)->pluck('course_module_id')->map(fn($id) => (int) $id)->all();

        // Find first incomplete module
        foreach ($modules as $module) {
            if (!in_array((int) $module->id, $completedIds, true)) {
                return (int) $module->id;
            }
        }

        // All completed — return last module
        return (int) $modules->last()->id;
    }

    /**
     * Calculate the progress percentage of the course.
     */
    public function getProgressPercentage(): int
    {
        if (!$this->relationLoaded('course') || !$this->course) {
            return 0;
        }

        $totalModules = $this->course->modules()->count();
        if ($totalModules === 0) {
            return 0;
        }

        $completedModules = $this->progress()->where('completed', true)->count();
        
        return (int) round(($completedModules / $totalModules) * 100);
    }

    /**
     * Check if enrollment is 100% complete.
     */
    public function isFullyCompleted(): bool
    {
        return $this->getProgressPercentage() >= 100;
    }
}

