<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'template_id',
        'template_version',
        'trainer_id',
        'trainer_contribution_scheme',
        'trainer_revenue_percent',
        'trainer_scheme_accepted_at',
        'description',
        'level',
        'status',
        'price',
        'free_access_mode',
        'duration',
        'media',
        'media_type',
        'card_thumbnail',
        'discount_percent',
        'discount_start',
        'discount_end',
        'user_id',
        'rejection_reason',
        'approved_at',
        'rejected_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'discount_start' => 'datetime',
        'discount_end' => 'datetime',
        'trainer_scheme_accepted_at' => 'datetime',
        'expenses_json',
    ];

    // protected $casts = [
    //     'expenses_json' => 'array',
    //     'discount_start' => 'date',
    //     'discount_end' => 'date',
    // ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function template()
    {
        return $this->belongsTo(CourseTemplate::class, 'template_id');
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('order_no');
    }

    public function units()
    {
        return $this->hasMany(CourseUnit::class)->orderBy('unit_no');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'course_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Enrollments relation (students enrolled to this course)
     */
    public function enrollments()
    {
        return $this->hasMany(\App\Models\Enrollment::class);
    }

    /**
     * Payments relation (payments made for this course)
     */


    /**
     * Manual payments relation (QRIS proof uploads)
     */
    public function manualPayments()
    {
        return $this->hasMany(\App\Models\ManualPayment::class);
    }

    /**
     * Approver relation (admin who approved/rejected)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getCardThumbnailUrlAttribute(): ?string
    {
        $thumbnail = trim((string) ($this->card_thumbnail ?? ''));
        if ($thumbnail !== '') {
            return $this->resolvePublicImageUrl($thumbnail);
        }

        $media = trim((string) ($this->media ?? ''));
        $mediaType = strtolower(trim((string) ($this->media_type ?? '')));
        if ($media !== '' && ($mediaType === 'image' || $this->looksLikeImagePath($media))) {
            return $this->resolvePublicImageUrl($media);
        }

        return null;
    }

    private function looksLikeImagePath(string $path): bool
    {
        $ext = strtolower(pathinfo(parse_url($path, PHP_URL_PATH) ?? $path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'], true);
    }

    private function resolvePublicImageUrl(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $normalized = str_replace('\\', '/', $path);
        $normalized = preg_replace('#^\./#', '', $normalized) ?? $normalized;
        $normalized = ltrim($normalized, '/');

        if (str_starts_with($normalized, 'public/')) {
            $normalized = ltrim(substr($normalized, 7), '/');
        }

        if (str_starts_with($normalized, 'storage/')) {
            return asset($normalized);
        }

        if (str_starts_with($normalized, 'uploads/')) {
            return asset($normalized);
        }

        if (Storage::disk('public')->exists($normalized)) {
            return Storage::disk('public')->url($normalized);
        }

        return asset('storage/' . $normalized);
    }
}
