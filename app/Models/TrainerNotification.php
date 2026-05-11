<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerNotification extends Model
{
    protected $fillable = [
        'trainer_id',
        'type',
        'title',
        'message',
        'data',
        'invitation_status',
        'responded_at',
        'read_at',
        'expires_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'responded_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function effectiveInvitationStatus(): string
    {
        if ($this->expires_at && $this->expires_at->isPast() && $this->invitation_status === 'pending') {
            return 'expired';
        }

        return (string) ($this->data['invitation_status'] ?? $this->invitation_status ?? 'pending');
    }
}
