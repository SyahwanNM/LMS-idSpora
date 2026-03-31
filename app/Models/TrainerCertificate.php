<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerCertificate extends Model
{
    protected $fillable = [
        'trainer_id',
        'certifiable_type',
        'certifiable_id',
        'activity_code',
        'type_code',
        'sequence',
        'certificate_number',
        'issued_at',
        'issued_by',
        'status',
        'file_path',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function certifiable()
    {
        return $this->morphTo();
    }
}

