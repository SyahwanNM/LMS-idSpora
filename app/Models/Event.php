<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'trainer_id',
        'title',
        'image',
        'vbg_path',
        'certificate_path',
        'attendance_path',
        'module_path',
        'material_status',
        'material_approved_at',
        'material_approved_by',
        'material_rejection_reason',
        'module_submitted_at',
        'module_verified_at',
        'module_verified_by',
        'module_rejected_at',
        'module_rejected_by',
        'module_rejection_reason',
        'certificate_logo',
        'certificate_signature',
        'certificate_template',
        'speaker',
        'materi',
        'jenis',
        'short_description',
        'description',
        'terms_and_conditions',
        'location',
        'price',
        'discount_percentage',
        'discount_until',
        'event_time',
        'event_time_end',
        'event_date',
        'material_deadline',
        'material_revision_deadline',
        'benefit',
        'maps_url',
        'latitude',
        'longitude',
        'zoom_link',
        // attendance QR one-time fields
        'attendance_qr_token',
        'attendance_qr_image',
        'attendance_qr_generated_at',
        // legacy JSON storage (backward compatible)
        'schedule_json',
        'expenses_json',
        'manage_action',
        'is_reseller_event',
    ];

    protected $casts = [
        'event_date' => 'date',
        'material_deadline' => 'datetime',
        'material_revision_deadline' => 'datetime',
        'event_time' => 'datetime:H:i',
        'event_time_end' => 'datetime:H:i',
        'module_submitted_at' => 'datetime',
        'module_verified_at' => 'datetime',
        'module_rejected_at' => 'datetime',
        'discount_until' => 'date',
        'material_approved_at' => 'datetime',
        'price' => 'decimal:2',
        'discount_percentage' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'schedule_json' => 'array',
        'expenses_json' => 'array',
        'is_reseller_event' => 'boolean',
        'certificate_logo' => 'array',
        'certificate_signature' => 'array',
        'module_path' => 'array', // Added to support multiple trainer modules
    ];

    /**
     * Count how many operational documents have been uploaded.
     */
    public function getDocumentsCompletedCountAttribute(): int
    {
        // Count completed items for the UI-perceived requirements.
        // Business rule (used across admin views):
        // - For offline-only events (has maps link, no zoom link) required items: Module, Attendance (2 items)
        // - Otherwise required items: Virtual Background, Module, Attendance (3 items)
        $hasVbg = !empty($this->vbg_path);
        $hasModule = !empty($this->module_path);
        $hasAttendance = !empty($this->attendance_path) || !empty($this->attendance_qr_image) || !empty($this->attendance_qr_token);

        $isOfflineOnly = (!empty($this->maps_url) && empty($this->zoom_link));

        // Return the raw count of completed items (not the denominator-aware percent).
        $count = 0;
        if (!$isOfflineOnly && $hasVbg) {
            $count++;
        }
        if ($hasModule) {
            $count++;
        }
        if ($hasAttendance) {
            $count++;
        }
        return (int) max(0, $count);
    }

    /**
     * Percentage (0-100) of document completeness based on 3 required docs.
     */
    public function getDocumentsCompletionPercentAttribute(): int
    {
        // Determine denominator according to offline/online rule
        $isOfflineOnly = (!empty($this->maps_url) && empty($this->zoom_link));
        $total = $isOfflineOnly ? 2 : 3;
        $done = (int) $this->documents_completed_count;
        $done = max(0, min($total, $done));
        if ($total === 0) return 0;
        if ($done === $total) return 100;
        return (int) floor(($done / $total) * 100);
    }

    public function getModuleSubmissionUrlAttribute(): ?string
    {
        return $this->buildPublicFileUrl($this->module_path, true);
    }

    public function getModuleFileUrlAttribute(): ?string
    {
        return $this->buildPublicFileUrl($this->module_path, true);
    }

    public function getVbgFileUrlAttribute(): ?string
    {
        return $this->buildPublicFileUrl($this->vbg_path, true);
    }

    public function getAttendanceQrImageUrlAttribute(): ?string
    {
        $path = trim((string) ($this->attendance_qr_image ?? ''));
        if ($path === '') {
            return null;
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

        // Force QR image to be served from /uploads/* (legacy public route).
        // Do not check filesystem; just map deterministically.
        if (str_starts_with($normalized, 'uploads/')) {
            return asset($normalized);
        }
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = ltrim(substr($normalized, 8), '/');
        }
        return asset('uploads/' . $normalized);
    }

    private function buildPublicFileUrl(?string $path, bool $forceUploads = false): ?string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $normalized = str_replace('\\', '/', $path);
        $normalized = preg_replace('#^\./#', '', $normalized) ?? $normalized;
        $normalized = ltrim($normalized, '/');

        // Common legacy prefixes that sometimes get stored in DB
        if (str_starts_with($normalized, 'public/')) {
            $normalized = ltrim(substr($normalized, 7), '/');
        }
        if (str_starts_with($normalized, 'storage/app/public/')) {
            $normalized = ltrim(substr($normalized, 19), '/');
        }

        // If already an uploads path, serve it directly
        if (str_starts_with($normalized, 'uploads/')) {
            return asset($normalized);
        }

        // If a Storage::url() output was stored ("storage/...") map it to uploads.
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = ltrim(substr($normalized, 8), '/');
            return asset('uploads/' . $normalized);
        }

        // For module links we force mapping to /uploads/* (legacy behavior) without checking the filesystem.
        if ($forceUploads) {
            return asset('uploads/' . $normalized);
        }

        // Default behavior for non-module usages (if any)
        return asset($normalized);
    }

    // Method untuk menghitung harga setelah diskon
    public function getDiscountedPriceAttribute()
    {
        if ($this->discount_percentage > 0) {
            return $this->price * (1 - $this->discount_percentage / 100);
        }
        return $this->price;
    }

    // Method untuk mengecek apakah ada diskon
    public function hasDiscount()
    {
        return $this->discount_percentage > 0;
    }

    // Relationship: event has many registrations
    public function registrationsActive()
    {
        return $this->hasMany(EventRegistration::class)->where('status', 'active');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function scheduleItems()
    {
        return $this->hasMany(EventScheduleItem::class);
    }

    public function expenses()
    {
        return $this->hasMany(EventExpense::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function trainerModules()
    {
        return $this->hasMany(\App\Models\EventTrainerModule::class);
    }

    public function approvedTrainerModules()
    {
        return $this->hasMany(\App\Models\EventTrainerModule::class)->where('status', 'approved');
    }

    public function getStartAtAttribute(): ?Carbon
    {
        if (empty($this->event_date))
            return null;
        $dateStr = $this->event_date instanceof Carbon ? $this->event_date->format('Y-m-d') : (string) $this->event_date;
        $timeStr = '00:00:00';
        if (!empty($this->event_time)) {
            $timeStr = $this->event_time instanceof Carbon ? $this->event_time->format('H:i:s') : (is_string($this->event_time) ? $this->event_time : '00:00:00');
        }
        try {
            return Carbon::parse($dateStr . ' ' . $timeStr, config('app.timezone'));
        } catch (\Throwable $ex) {
            return null;
        }
    }

    /**
     * Get the image URL attribute.
     * Ensures consistent URL generation for event images.
     * Uses same approach as User avatar_url for consistency.
     */
    public function getImageUrlAttribute(): ?string
    {
        $image = (string) ($this->image ?? '');
        if ($image === '') {
            return null;
        }

        // External URL (e.g., from external source)
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        // Deterministic mapping (no filesystem checks)
        $normalized = str_replace('\\', '/', trim($image));
        $normalized = preg_replace('#^\./#', '', $normalized) ?? $normalized;
        $normalized = ltrim($normalized, '/');

        // Common stored prefixes
        if (str_starts_with($normalized, 'public/')) {
            $normalized = ltrim(substr($normalized, 7), '/');
        }
        if (str_starts_with($normalized, 'storage/app/public/')) {
            $normalized = ltrim(substr($normalized, 19), '/');
        }
        if (str_starts_with($normalized, 'uploads/')) {
            return asset($normalized);
        }
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = ltrim(substr($normalized, 8), '/');
        }

        // If path already contains folders, serve it under /uploads/<path>
        if (str_contains($normalized, '/')) {
            return asset('uploads/' . $normalized);
        }

        // If it's just a filename, event posters conventionally live under uploads/events/
        return asset('uploads/events/' . $normalized);
    }

    public function getEndAtAttribute(): ?Carbon
    {
        $start = $this->start_at;
        if (!$start)
            return null;
        $timeStr = null;
        if (!empty($this->event_time_end)) {
            $timeStr = $this->event_time_end instanceof Carbon ? $this->event_time_end->format('H:i:s') : (is_string($this->event_time_end) ? $this->event_time_end : null);
        }
        if ($timeStr) {
            $dateStr = $start->format('Y-m-d');
            try {
                return Carbon::parse($dateStr . ' ' . $timeStr, config('app.timezone'));
            } catch (\Throwable $ex) {
                return (clone $start)->endOfDay();
            }
        }
        return (clone $start)->endOfDay();
    }

    /**
     * Determine if event finished (end time < now()).
     */
    public function isFinished(): bool
    {
        $end = $this->end_at;
        return $end ? $end->lt(Carbon::now()) : false;
    }

    /**
     * Scope: active (not finished). Treat events without date as active (legacy drafts).
     */
    public function scopeActive($query)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        return $query->where(function ($q) use ($now) {
            $q->whereNull('event_date')
                ->orWhereRaw("TIMESTAMP(event_date, COALESCE(event_time_end, COALESCE(event_time,'23:59:59'))) >= ?", [$now]);
        });
    }

    /**
     * Scope: finished (past events).
     */
    public function scopeFinished($query)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        return $query->whereNotNull('event_date')
            ->whereRaw("TIMESTAMP(event_date, COALESCE(event_time_end, COALESCE(event_time,'23:59:59'))) < ?", [$now]);
    }

    /**
     * Count schedule items stored in schedule_json.
     */
    public function getScheduleCountAttribute(): int
    {
        if ($this->relationLoaded('scheduleItems'))
            return $this->scheduleItems->count();
        // fallback to JSON
        return is_array($this->schedule_json) ? count($this->schedule_json) : $this->scheduleItems()->count();
    }

    /**
     * Sum total expenses from expenses_json rows (expects each row to have 'total').
     */
    public function getExpensesTotalAttribute(): float
    {
        if ($this->relationLoaded('expenses'))
            return (float) $this->expenses->sum('total');
        if (is_array($this->expenses_json)) {
            return (float) array_sum(array_map(fn($row) => (float) ($row['total'] ?? 0), $this->expenses_json));
        }
        return (float) $this->expenses()->sum('total');
    }
}