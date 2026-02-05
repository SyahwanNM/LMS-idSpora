<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
