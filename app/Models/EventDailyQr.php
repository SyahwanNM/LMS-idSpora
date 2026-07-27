<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EventDailyQr extends Model
{
    protected $fillable = [
        'event_id',
        'qr_date',
        'day_number',
        'token',
        'qr_image',
    ];

    protected $casts = [
        'qr_date' => 'date',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function dailyAttendances()
    {
        return $this->hasMany(EventDailyAttendance::class);
    }

    /**
     * URL for the QR image (mirrors Event::getAttendanceQrImageUrlAttribute logic).
     */
    public function getQrImageUrlAttribute(): ?string
    {
        $path = trim((string) ($this->qr_image ?? ''));
        if ($path === '') return null;
        if (preg_match('#^https?://#i', $path)) return $path;

        $normalized = str_replace('\\', '/', $path);
        $normalized = ltrim(preg_replace('#^\./#', '', $normalized) ?? $normalized, '/');
        if (str_starts_with($normalized, 'public/'))   $normalized = ltrim(substr($normalized, 7), '/');
        if (str_starts_with($normalized, 'storage/'))  $normalized = ltrim(substr($normalized, 8), '/');

        if (str_starts_with($normalized, 'uploads/')) {
            if (file_exists(public_path($normalized))) {
                return asset($normalized);
            }
            $withoutUploads = ltrim(substr($normalized, 8), '/');
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($withoutUploads)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->url($withoutUploads);
            }
        }

        if (file_exists(public_path('uploads/' . $normalized))) {
            return asset('uploads/' . $normalized);
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($normalized);
    }
}
