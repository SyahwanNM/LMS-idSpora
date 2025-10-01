<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'image',
        'speaker',
        'description',
        'location',
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
}
