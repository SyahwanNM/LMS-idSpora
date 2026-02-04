<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Event extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'image',
        'vbg_path',
        'certificate_path',
        'attendance_path',
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
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i',
        'event_time_end' => 'datetime:H:i',
        'discount_until' => 'date',
        'price' => 'decimal:2',
        'discount_percentage' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'schedule_json' => 'array',
        'expenses_json' => 'array',
        'certificate_logo' => 'array',
        'certificate_signature' => 'array',
    ];

    /**
     * Count how many operational documents have been uploaded.
     */
    public function getDocumentsCompletedCountAttribute(): int
    {
        $count = 0;
        if(!empty($this->vbg_path)) $count++;
        if(!empty($this->certificate_path)) $count++;
        // Absensi dianggap selesai bila ada file attendance atau QR attendance aktif
        $hasAttendance = !empty($this->attendance_path)
            || !empty($this->attendance_qr_image)
            || !empty($this->attendance_qr_token);
        if($hasAttendance) $count++;
        return $count;
    }

    /**
     * Percentage (0-100) of document completeness based on 3 required docs.
     */
    public function getDocumentsCompletionPercentAttribute(): int
    {
        $total = 3; // Virtual Background, Sertifikat, Absensi (QR/File)
        $done = max(0, min(3, (int) $this->documents_completed_count));
        return (int) floor(($done / $total) * 100);
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

    public function getStartAtAttribute(): ?Carbon
    {
        if(empty($this->event_date)) return null;
        $dateStr = $this->event_date instanceof Carbon ? $this->event_date->format('Y-m-d') : (string) $this->event_date;
        $timeStr = '00:00:00';
        if(!empty($this->event_time)){
            $timeStr = $this->event_time instanceof Carbon ? $this->event_time->format('H:i:s') : (is_string($this->event_time) ? $this->event_time : '00:00:00');
        }
        try { return Carbon::parse($dateStr.' '.$timeStr, config('app.timezone')); } catch (\Throwable $ex) { return null; }
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
        
        // Normalize path - remove 'storage/' prefix if exists
        $imagePath = ltrim(str_replace('storage/', '', $image), '/');
        
        // Extract filename from path
        $filename = basename($imagePath);
        
        // Check if file exists in events folder (try multiple possible paths)
        $possiblePaths = [
            'events/' . $filename,  // events/filename.png
            $imagePath,              // events/filename.png (if already has events/)
            'events/' . $imagePath, // events/events/filename.png (if path already has events/)
        ];
        
        // Remove duplicates
        $possiblePaths = array_unique($possiblePaths);
        
        // Find first existing file
        foreach ($possiblePaths as $path) {
            $fullPath = storage_path('app/public/' . $path);
            if (file_exists($fullPath) && is_file($fullPath)) {
                // File exists, return URL
                return asset('storage/' . $path);
            }
        }
        
        // File not found, but return URL anyway (browser will show broken image or fallback)
        // This allows onerror handler in views to work
        if (str_starts_with($imagePath, 'events/')) {
            return asset('storage/' . $imagePath);
        }
        return asset('storage/events/' . $filename);
    }

    public function getEndAtAttribute(): ?Carbon
    {
        $start = $this->start_at;
        if(!$start) return null;
        $timeStr = null;
        if(!empty($this->event_time_end)){
            $timeStr = $this->event_time_end instanceof Carbon ? $this->event_time_end->format('H:i:s') : (is_string($this->event_time_end) ? $this->event_time_end : null);
        }
        if($timeStr){
            $dateStr = $start->format('Y-m-d');
            try { return Carbon::parse($dateStr.' '.$timeStr, config('app.timezone')); } catch (\Throwable $ex) { return (clone $start)->endOfDay(); }
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
        return $query->where(function($q) use ($now){
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
        if($this->relationLoaded('scheduleItems')) return $this->scheduleItems->count();
        // fallback to JSON
        return is_array($this->schedule_json) ? count($this->schedule_json) : $this->scheduleItems()->count();
    }

    /**
     * Sum total expenses from expenses_json rows (expects each row to have 'total').
     */
    public function getExpensesTotalAttribute(): float
    {
        if($this->relationLoaded('expenses')) return (float) $this->expenses->sum('total');
        if(is_array($this->expenses_json)){
            return (float) array_sum(array_map(fn($row) => (float) ($row['total'] ?? 0), $this->expenses_json));
        }
        return (float) $this->expenses()->sum('total');
    }
}
