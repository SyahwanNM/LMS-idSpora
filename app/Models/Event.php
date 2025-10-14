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
        'speaker',
        'materi',
        'jenis',
        'description',
        'terms_and_conditions',
        'location',
        'whatsapp_link',
        'price',
        'discount_percentage',
        'event_time',
        'event_date',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i',
        'price' => 'decimal:2',
        'discount_percentage' => 'integer',
    ];

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
}
