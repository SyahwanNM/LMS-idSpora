<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificates extends Model
{
    protected $fillable = [
        'user_id',
        'template_id',
        'file_path',
        'expiry_date',
        'generated_at',
        'certificate_code',
        'certifiable_id',
        'certifiable_type',
    ];

    // Relationships to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to certificate template
    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class);
    }

    // Polymorphic relationship to certifiable entities (Course, Event, etc.)
    public function certifiable()
    {
        return $this->morphTo();
    }
}
