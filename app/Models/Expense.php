<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'description', 'amount', 'category', 'expense_date', 'status', 'proof_of_payment', 'rejected_reason'
    ];

    protected $casts = [
        'expense_date' => 'date'
    ];
}
