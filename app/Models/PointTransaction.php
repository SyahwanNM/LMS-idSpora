<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'source',
        'source_id',
        'description',
    ];

    /**
     * Get the user that owns the point transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
