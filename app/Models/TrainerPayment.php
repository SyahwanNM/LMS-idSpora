<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerPayment extends Model
{
    protected $fillable = [
        'trainer_id', 'amount', 'month', 'year', 'proof_of_payment', 'salary_slip', 'note'
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
