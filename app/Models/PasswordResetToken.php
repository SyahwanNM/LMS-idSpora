<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetToken extends Model
{
    public $timestamps = true;
    
    protected $fillable = [
        'email',
        'token',
        'verification_code',
        'expires_at',
        'is_used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean'
    ];

    public function isExpired()
    {
        return $this->expires_at < now();
    }

    public function isValid()
    {
        return !$this->is_used && !$this->isExpired();
    }
}
