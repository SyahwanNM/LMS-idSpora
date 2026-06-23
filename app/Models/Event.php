<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\TrainerCertificateAssets;
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
        'module_path',
        'module_submission_path',
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
        'price_offline',
        'price_online',
        'max_participants',
        'discount_percentage',
        'discount_until',
        'event_time',
        'event_time_end',
        'event_date',
        'event_until_date',
        'event_until_time',
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
        'is_published',
        'published_at',
        'accept_online_payment',
        'accept_manual_transfer',
        'bank_account_number',
        'bank_name',
        'bank_account_holder',
        'start_submission',
        'until_submission',
        'announcement_date',
        'until_submission_2',
        'price_stage2',
        'finalist_payment_start',
        'finalist_payment_end',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_until_date' => 'date',
        'material_deadline' => 'datetime',
        'material_revision_deadline' => 'datetime',
        'event_time' => 'datetime:H:i',
        'event_time_end' => 'datetime:H:i',
        'discount_until' => 'date',
        'price' => 'decimal:2',
        'discount_percentage' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'schedule_json' => 'array',
        'expenses_json' => 'array',
        'benefit' => 'array',
        'is_reseller_event' => 'boolean',
        'accept_online_payment' => 'boolean',
        'accept_manual_transfer' => 'boolean',
        'certificate_logo' => 'array',
        'certificate_signature' => 'array',
        'start_submission' => 'datetime',
        'until_submission' => 'datetime',
        'announcement_date' => 'datetime',
        'until_submission_2' => 'datetime',
        'price_stage2' => 'decimal:2',
        'finalist_payment_start' => 'datetime',
        'finalist_payment_end' => 'datetime',
    ];

    protected $virtualAttributes = [];

    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->virtualAttributes = [];
        return parent::setRawAttributes($attributes, $sync);
    }

    public function setVirtualAttribute($key, $value)
    {
        $this->virtualAttributes[$key] = $value;
    }

    public function getVirtualAttribute($key, $fallback = null)
    {
        return $this->virtualAttributes[$key] ?? $fallback;
    }

    // Setters
    public function setModulePathAttribute($value) { $this->setVirtualAttribute('module_path', $value); }
    public function setMaterialStatusAttribute($value) { $this->setVirtualAttribute('material_status', $value); }
    public function setModuleSubmissionPathAttribute($value) { $this->setVirtualAttribute('module_submission_path', $value); }
    public function setModuleSubmittedAtAttribute($value) { $this->setVirtualAttribute('module_submitted_at', $value); }
    public function setModuleVerifiedAtAttribute($value) { $this->setVirtualAttribute('module_verified_at', $value); }
    public function setModuleVerifiedByAttribute($value) { $this->setVirtualAttribute('module_verified_by', $value); }
    public function setMaterialApprovedAtAttribute($value) { $this->setVirtualAttribute('material_approved_at', $value); }
    public function setMaterialApprovedByAttribute($value) { $this->setVirtualAttribute('material_approved_by', $value); }
    public function setMaterialRejectionReasonAttribute($value) { $this->setVirtualAttribute('material_rejection_reason', $value); }
    public function setModuleRejectedAtAttribute($value) { $this->setVirtualAttribute('module_rejected_at', $value); }
    public function setModuleRejectedByAttribute($value) { $this->setVirtualAttribute('module_rejected_by', $value); }
    public function setModuleRejectionReasonAttribute($value) { $this->setVirtualAttribute('module_rejection_reason', $value); }

    // Getters
    public function getModulePathAttribute()
    {
        if (array_key_exists('module_path', $this->virtualAttributes)) {
            return $this->virtualAttributes['module_path'];
        }
        $latest = $this->trainerModules()->latest()->first();
        return $latest ? $latest->path : null;
    }

    public function getMaterialStatusAttribute()
    {
        if (array_key_exists('material_status', $this->virtualAttributes)) {
            return $this->virtualAttributes['material_status'];
        }
        if (isset($this->attributes['material_status']) && $this->attributes['material_status'] !== null) {
            return $this->attributes['material_status'];
        }
        $latest = $this->trainerModules()->latest()->first();
        return $latest ? $latest->status : 'pending';
    }

    public function getModuleSubmissionPathAttribute()
    {
        if (array_key_exists('module_submission_path', $this->virtualAttributes)) {
            return $this->virtualAttributes['module_submission_path'];
        }
        $latest = $this->trainerModules()->latest()->first();
        return $latest ? $latest->path : null;
    }

    public function getModuleSubmittedAtAttribute()
    {
        $val = array_key_exists('module_submitted_at', $this->virtualAttributes)
            ? $this->virtualAttributes['module_submitted_at']
            : $this->trainerModules()->latest()->first()?->created_at;
        return $val ? \Carbon\Carbon::parse($val) : null;
    }

    public function getModuleVerifiedAtAttribute()
    {
        $val = array_key_exists('module_verified_at', $this->virtualAttributes)
            ? $this->virtualAttributes['module_verified_at']
            : $this->trainerModules()->where('status', 'approved')->latest()->first()?->reviewed_at;
        return $val ? \Carbon\Carbon::parse($val) : null;
    }

    public function getModuleVerifiedByAttribute()
    {
        if (array_key_exists('module_verified_by', $this->virtualAttributes)) {
            return $this->virtualAttributes['module_verified_by'];
        }
        return $this->trainerModules()->where('status', 'approved')->latest()->first()?->reviewed_by;
    }

    public function getMaterialApprovedAtAttribute()
    {
        $val = array_key_exists('material_approved_at', $this->virtualAttributes)
            ? $this->virtualAttributes['material_approved_at']
            : $this->trainerModules()->where('status', 'approved')->latest()->first()?->reviewed_at;
        return $val ? \Carbon\Carbon::parse($val) : null;
    }

    public function getMaterialApprovedByAttribute()
    {
        if (array_key_exists('material_approved_by', $this->virtualAttributes)) {
            return $this->virtualAttributes['material_approved_by'];
        }
        return $this->trainerModules()->where('status', 'approved')->latest()->first()?->reviewed_by;
    }

    public function getMaterialRejectionReasonAttribute()
    {
        if (array_key_exists('material_rejection_reason', $this->virtualAttributes)) {
            return $this->virtualAttributes['material_rejection_reason'];
        }
        return $this->trainerModules()->where('status', 'rejected')->latest()->first()?->rejection_reason;
    }

    public function getModuleRejectedAtAttribute()
    {
        $val = array_key_exists('module_rejected_at', $this->virtualAttributes)
            ? $this->virtualAttributes['module_rejected_at']
            : $this->trainerModules()->where('status', 'rejected')->latest()->first()?->reviewed_at;
        return $val ? \Carbon\Carbon::parse($val) : null;
    }

    public function getModuleRejectedByAttribute()
    {
        if (array_key_exists('module_rejected_by', $this->virtualAttributes)) {
            return $this->virtualAttributes['module_rejected_by'];
        }
        return $this->trainerModules()->where('status', 'rejected')->latest()->first()?->reviewed_by;
    }

    public function getModuleRejectionReasonAttribute()
    {
        if (array_key_exists('module_rejection_reason', $this->virtualAttributes)) {
            return $this->virtualAttributes['module_rejection_reason'];
        }
        return $this->trainerModules()->where('status', 'rejected')->latest()->first()?->rejection_reason;
    }


    public function getHasApprovedModulesAttribute(): bool
    {
        $assignedTrainerIds = collect();
        if ($this->trainer_id) {
            $assignedTrainerIds->push((int) $this->trainer_id);
        }
        $speakerTrainerIds = $this->speakers()->whereNotNull('trainer_id')->pluck('trainer_id')->map(fn($id) => (int) $id);
        $assignedTrainerIds = $assignedTrainerIds->merge($speakerTrainerIds)->unique()->values();

        if ($assignedTrainerIds->isNotEmpty()) {
            $approvedTrainerIds = $this->approvedTrainerModules()->distinct('trainer_id')->pluck('trainer_id')->map(fn($id) => (int) $id)->toArray();
            
            foreach ($assignedTrainerIds as $tId) {
                if (!in_array($tId, $approvedTrainerIds, true)) {
                    return false;
                }
            }
            return true;
        }

        // Tidak ada trainer yang di-assign → secara otomatis benar (karena tidak ada modul yang perlu di-upload)
        return true;
    }

    /**
     * Count how many operational documents have been uploaded.
     */
    public function getDocumentsCompletedCountAttribute(): int
    {
        $hasVbg = !empty($this->vbg_path);
        $isOfflineOnly = (!empty($this->maps_url) && empty($this->zoom_link));

        if ($this->jenis === 'Lomba') {
            $count = 0;
            if (!$isOfflineOnly && $hasVbg) {
                $count++;
            }
            return $count;
        }

        $hasModule = $this->has_approved_modules;

        $isMultiDay = !empty($this->event_until_date)
            && \Carbon\Carbon::parse($this->event_until_date)->gt(\Carbon\Carbon::parse($this->event_date));
        $hasDailyAbs = false;
        if ($isMultiDay && $this->id) {
            $hasDailyAbs = \App\Models\EventDailyQr::where('event_id', $this->id)->exists();
        }
        $hasAttendance = !empty($this->attendance_path) || !empty($this->attendance_qr_image) || !empty($this->attendance_qr_token) || $hasDailyAbs;

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
        if ($this->jenis === 'Lomba') {
            $total = $isOfflineOnly ? 0 : 1;
        } else {
            $total = $isOfflineOnly ? 2 : 3;
        }
        $done = (int) $this->documents_completed_count;
        $done = max(0, min($total, $done));
        if ($total === 0)
            return 100;
        if ($done === $total)
            return 100;
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

    public function trainerAssignments()
    {
        return $this->hasMany(\App\Models\TrainerAssignment::class);
    }

    public function approvedTrainerModules()
    {
        return $this->hasMany(\App\Models\EventTrainerModule::class)->where('status', 'approved');
    }

    public function speakers()
    {
        return $this->hasMany(\App\Models\EventSpeaker::class)->orderBy('order');
    }

    public function dailyAttendances()
    {
        return $this->hasManyThrough(
            EventDailyAttendance::class,
            EventRegistration::class,
            'event_id',
            'event_registration_id',
            'id',
            'id'
        );
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
        // If event_until_date is set, use it (+ event_until_time or 23:59:59) as the deadline
        if (!empty($this->event_until_date)) {
            $dateStr = $this->event_until_date instanceof Carbon
                ? $this->event_until_date->format('Y-m-d')
                : (string) $this->event_until_date;
            $timeStr = '23:59:59';
            if (!empty($this->event_until_time)) {
                $timeStr = is_string($this->event_until_time)
                    ? $this->event_until_time
                    : ($this->event_until_time instanceof Carbon ? $this->event_until_time->format('H:i:s') : '23:59:59');
            }
            try {
                return Carbon::parse($dateStr . ' ' . $timeStr, config('app.timezone'));
            } catch (\Throwable $ex) {}
        }

        // Fallback: event_date + event_time_end (or end of day)
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
     * Determine if event finished (end_at < now).
     * When event_until_date is set, it acts as the real end deadline.
     */
    public function isFinished(): bool
    {
        $end = $this->end_at;
        return $end ? $end->lt(Carbon::now()) : false;
    }

    /**
     * Scope: active (not finished).
     * Uses COALESCE(event_until_date, event_date) + COALESCE(event_until_time, event_time_end, event_time)
     */
    public function scopeActive($query)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $rawSql = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite'
            ? "COALESCE(event_until_date, event_date) || ' ' || COALESCE(event_until_time, event_time_end, event_time, '23:59:59')"
            : "TIMESTAMP(COALESCE(event_until_date, event_date), COALESCE(event_until_time, event_time_end, event_time, '23:59:59'))";
        return $query->where(function ($q) use ($now, $rawSql) {
            $q->whereNull('event_date')
                ->orWhereRaw("{$rawSql} >= ?", [$now]);
        });
    }

    /**
     * Scope: finished (past events).
     */
    public function scopeFinished($query)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $rawSql = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite'
            ? "COALESCE(event_until_date, event_date) || ' ' || COALESCE(event_until_time, event_time_end, event_time, '23:59:59')"
            : "TIMESTAMP(COALESCE(event_until_date, event_date), COALESCE(event_until_time, event_time_end, event_time, '23:59:59'))";
        return $query->whereNotNull('event_date')
            ->whereRaw("{$rawSql} < ?", [$now]);
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

    public function trainerCertificateAssets()
    {
        return $this->morphMany(
            TrainerCertificateAsset::class,
            'certifiable'
        )->orderBy('order_no');
    }
}