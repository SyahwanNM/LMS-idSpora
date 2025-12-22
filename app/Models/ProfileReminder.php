<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileReminder extends Model
{
    protected $fillable = [
        'user_id',
        'last_shown_at',
        'dismiss_count',
        'is_active',
    ];

    protected $casts = [
        'last_shown_at' => 'datetime',
        'dismiss_count' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
