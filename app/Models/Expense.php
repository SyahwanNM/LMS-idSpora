<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'description', 'amount', 'category', 'expense_date'
    ];

    protected $casts = [
        'expense_date' => 'date'
    ];
}