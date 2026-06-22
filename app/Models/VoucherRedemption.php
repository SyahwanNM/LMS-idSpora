<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class VoucherRedemption extends Model
{
    protected $fillable = [
        'user_id',
        'voucher_id',
        'code',
        'is_used',
        'used_at',
        'redeemed_at',
        'expires_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'redeemed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that redeemed the voucher.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent voucher definition.
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Check if this redeemed voucher is usable.
     */
    public function isUsable(): bool
    {
        if ($this->is_used) {
            return false;
        }

        if ($this->expires_at && Carbon::parse($this->expires_at)->isPast()) {
            return false;
        }

        // Lazy load relationship if needed
        if (!$this->relationLoaded('voucher')) {
            $this->load('voucher');
        }

        if ($this->voucher && !$this->voucher->active) {
            return false;
        }

        return true;
    }
}
