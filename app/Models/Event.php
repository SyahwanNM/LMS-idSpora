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
        'speaker',
        'materi',
        'jenis',
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
    ];

    /**
     * Count how many operational documents have been uploaded.
     */
    public function getDocumentsCompletedCountAttribute(): int
    {
        $count = 0;
        if(!empty($this->vbg_path)) $count++;
        if(!empty($this->certificate_path)) $count++;
        if(!empty($this->attendance_path)) $count++;
        return $count;
    }

    /**
     * Percentage (0-100) of document completeness based on 3 required docs.
     */
    public function getDocumentsCompletionPercentAttribute(): int
    {
        $total = 3; // virtual background, certificate, attendance
        $done = $this->documents_completed_count; // uses accessor above
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
