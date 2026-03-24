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
        'read_at',
        'expires_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
