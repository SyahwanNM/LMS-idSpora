<?php 

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // --- INFO UTAMA ---
            'id' => $this->id,
            'title' => $this->title,
            'speaker' => $this->speaker,
            'category'=> $this->jenis, // Webinar/Seminar
            'topic'=> $this->materi,   // Web Programming, dll
            'image_url' => $this->image_url, // Pakai accessor model kamu
            
            // --- DESKRIPSI ---
            'short_description' => $this->short_description,
            'full_description' => $this->description,
            'benefit' => $this->benefit,
            'terms' => $this->terms_and_conditions,

            // --- LOKASI (Array Lengkap) ---
            'location' => [
                'name' => $this->location,
                'maps_url' => $this->maps_url,
                'coordinates' => [
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                ],
                'zoom_link' => $this->zoom_link,
            ],

            // --- HARGA & DISKON ---
            'pricing'=> [
                'price_original' => (float) $this->price,
                'price_final' => (float) $this->discounted_price,
                'discount_percentage' => $this->discount_percentage,
                'is_discounted'=> $this->hasDiscount(),
                'is_free' => $this->discounted_price <= 0,
            ],

            // --- WAKTU ---
            'schedule_info' => [
                'date' => $this->start_at ? $this->start_at->format('Y-m-d') : null,
                'time_start' => $this->start_at ? $this->start_at->format('H:i') : null,
                'time_end' => $this->end_at ? $this->end_at->format('H:i') : null,
                'is_finished' => $this->isFinished(),
            ],

            // --- JADWAL DETIL ---
            'rundown' => $this->scheduleItems->isNotEmpty() 
                            ? $this->scheduleItems 
                            : $this->schedule_json,

            // --- TIMESTAMP ---
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}