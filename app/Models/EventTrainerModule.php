<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EventTrainerModule extends Model
{
    protected $fillable = [
        'event_id',
        'trainer_id',
        'original_name',
        'path',
        'survey_link',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'feedback_link',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getDownloadUrlAttribute(): string
    {
        if (preg_match('#^https?://#i', $this->path)) {
            return $this->path;
        }
        return Storage::disk('public')->url($this->path);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
