<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'points_required',
        'discount_type',
        'discount_value',
        'min_purchase',
        'expires_at',
        'usage_limit',
        'times_redeemed',
        'active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'active' => 'boolean',
        'points_required' => 'integer',
        'discount_value' => 'integer',
        'min_purchase' => 'integer',
        'times_redeemed' => 'integer',
    ];

    /**
     * Check if voucher is valid and can be redeemed.
     */
    public function isValid(): bool
    {
        if (!$this->active) {
            return false;
        }

        if ($this->expires_at && Carbon::parse($this->expires_at)->isPast()) {
            return false;
        }

        if ($this->usage_limit !== null && $this->times_redeemed >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount for a given original price.
     */
    public function calculateDiscount(float $originalPrice): float
    {
        if ($originalPrice < $this->min_purchase) {
            return 0.0;
        }

        if ($this->discount_type === 'percentage') {
            $discount = $originalPrice * ($this->discount_value / 100.0);
        } else {
            $discount = (float) $this->discount_value;
        }

        return (float) min($originalPrice, $discount);
    }
}
