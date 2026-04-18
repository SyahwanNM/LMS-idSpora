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
        $statusFromData = trim((string) data_get($this->data, 'invitation_status', ''));
        if ($statusFromData !== '') {
            return $statusFromData;
        }

        $statusFromColumn = trim((string) ($this->invitation_status ?? ''));
        if ($statusFromColumn !== '') {
            return $statusFromColumn;
        }

        return in_array($this->type, ['course_invitation', 'event_invitation'], true)
            ? 'pending'
            : '';
    }

    public function effectiveEntityType(): string
    {
        $entityType = trim((string) data_get($this->data, 'entity_type', ''));
        if ($entityType !== '') {
            return $entityType;
        }

        return match ((string) $this->type) {
            'course_invitation' => 'course',
            'event_invitation' => 'event',
            default => '',
        };
    }

    public function effectiveEntityId(): int
    {
        return (int) data_get($this->data, 'entity_id', 0);
    }
}
