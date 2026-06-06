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

    public function certifiable()
    {
        return $this->morphTo();
    }
}