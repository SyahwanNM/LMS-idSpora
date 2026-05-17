<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerCertificateAsset extends Model
{
    public const TYPE_LOGO = 'logo';
    public const TYPE_SIGNATURE = 'signature';

    protected $fillable = [
        'certifiable_type',
        'certifiable_id',
        'type',
        'name',
        'position',
        'image_path',
        'order_no',
    ];

    protected $casts = [
        'certifiable_id' => 'integer',
        'order_no' => 'integer',
    ];

    public function certifiable()
    {
        return $this->morphTo();
    }

    public function scopeLogo($query)
    {
        return $query->where('type', self::TYPE_LOGO);
    }

    public function scopeSignature($query)
    {
        return $query->where('type', self::TYPE_SIGNATURE);
    }

    public function isLogo(): bool
    {
        return $this->type === self::TYPE_LOGO;
    }

    public function isSignature(): bool
    {
        return $this->type === self::TYPE_SIGNATURE;
    }
}