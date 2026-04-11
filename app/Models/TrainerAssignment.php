<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainerAssignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'trainer_id',
        'event_id',
        'invitation_notification_id',
        'scheme_type',
        'legal_agreement_accepted_at',
        'legal_agreement_accepted_ip',
        'legal_agreement_accepted_user_agent',
        'sla_upload_deadline',
        'materials_uploaded_at',
        'status',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'scheme_type' => 'integer',
        'legal_agreement_accepted_at' => 'datetime',
        'sla_upload_deadline' => 'datetime',
        'materials_uploaded_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Scheme descriptions and requirements
     */
    public static function getSchemeDefinitions(): array
    {
        return [
            1 => [
                'label' => 'Beban Kerja Penuh',
                'percentage' => 35,
                'description' => 'Unggah Modul, Video, dan Kuis',
                'requirements' => ['modules', 'videos', 'quizzes'],
            ],
            2 => [
                'label' => 'Beban Kerja Menengah',
                'percentage' => 25,
                'description' => 'Unggah Modul dan Video',
                'requirements' => ['modules', 'videos'],
            ],
            3 => [
                'label' => 'Beban Kerja Ringan',
                'percentage' => 10,
                'description' => 'Unggah Video saja',
                'requirements' => ['videos'],
            ],
        ];
    }

    /**
     * Get scheme label
     */
    public function getSchemeLabel(): ?string
    {
        $schemes = self::getSchemeDefinitions();
        return $schemes[$this->scheme_type]['label'] ?? null;
    }

    /**
     * Get scheme percentage
     */
    public function getSchemePercentage(): ?int
    {
        $schemes = self::getSchemeDefinitions();
        return $schemes[$this->scheme_type]['percentage'] ?? null;
    }

    /**
     * Get scheme requirements
     */
    public function getSchemeRequirements(): array
    {
        $schemes = self::getSchemeDefinitions();
        return $schemes[$this->scheme_type]['requirements'] ?? [];
    }

    /**
     * Check if SLA is still active
     */
    public function isSlaActive(): bool
    {
        return $this->status === 'accepted' && $this->sla_upload_deadline?->isFuture();
    }

    /**
     * Get remaining hours for SLA
     */
    public function getRemainingHours(): int
    {
        if (!$this->isSlaActive()) {
            return 0;
        }
        return (int) $this->sla_upload_deadline->diffInHours(now());
    }

    /**
     * Relationships
     */
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function invitationNotification(): BelongsTo
    {
        return $this->belongsTo(TrainerNotification::class, 'invitation_notification_id');
    }
}
