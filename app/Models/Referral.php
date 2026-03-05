<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'user_id',
        'referred_user_id',
        'amount',
        'status',
        'description'
    ];
    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
