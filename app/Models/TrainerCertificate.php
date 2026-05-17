<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerCertificate extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_READY = 'ready';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REVOKED = 'revoked';

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

    public function scopeDraft($query) {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeReady($query) {
        return $query->where('status', self::STATUS_READY);
    }

    public function scopePublished($query) {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

     public function scopeRevoked($query)
    {
        return $query->where('status', self::STATUS_REVOKED);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isReady(): bool
    {
        return $this->status === self::STATUS_READY;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isRevoked(): bool
    {
        return $this->status === self::STATUS_REVOKED;
    }
}

