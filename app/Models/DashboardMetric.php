<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'snapshot_date',
        'users_count',
        'courses_count',
        'events_count',
        'revenue_total',
    ];
}
