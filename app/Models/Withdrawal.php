<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    // Izinkan kolom ini diisi oleh user
    protected $fillable = [
        'user_id',
        'amount',
        'bank_name',
        'account_number',
        'account_holder',
        'status',
        'proof_of_transfer',
        'rejected_reason'
    ];

    // Relasi ke User (Setiap penarikan milik satu user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}