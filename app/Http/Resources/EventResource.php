<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $hasZoom     = !empty($this->zoom_link);
        $hasLocation = !empty($this->location) && strtolower($this->location) !== 'online';
        $eventType   = $hasZoom && $hasLocation ? 'hybrid' : ($hasZoom ? 'online' : 'offline');

        return [
            'id'              => (int) $this->id,
            'title'           => (string) ($this->title ?? ''),
            'speaker'         => $this->speaker,
            'category'        => $this->jenis,
            'topic'           => $this->materi,
            'event_type'      => $eventType,
            'image_url'       => $this->image_url,
            'is_published'    => (bool) $this->is_published,

            'short_description' => $this->short_description,
            'description'       => $this->description,
            'benefit'           => $this->benefit,
            'terms'             => $this->terms_and_conditions,

            'location' => [
                'name'        => $this->location,
                'maps_url'    => $this->maps_url,
                'coordinates' => [
                    'latitude'  => $this->latitude  ? (float) $this->latitude  : null,
                    'longitude' => $this->longitude ? (float) $this->longitude : null,
                ],
                'zoom_link'   => $this->zoom_link,
            ],

            'pricing' => [
                'price_original'      => (float) ($this->price ?? 0),
                'price_final'         => (float) ($this->discounted_price ?? $this->price ?? 0),
                'discount_percentage' => (int) ($this->discount_percentage ?? 0),
                'is_discounted'       => method_exists($this->resource, 'hasDiscount') ? $this->hasDiscount() : false,
                'is_free'             => (float) ($this->discounted_price ?? $this->price ?? 0) <= 0,
            ],

            'schedule' => [
                'date'        => $this->start_at ? $this->start_at->format('Y-m-d') : null,
                'time_start'  => $this->start_at ? $this->start_at->format('H:i') : null,
                'time_end'    => $this->end_at   ? $this->end_at->format('H:i')   : null,
                'is_started'  => $this->start_at ? now()->gte($this->start_at) : false,
                'is_finished' => method_exists($this->resource, 'isFinished') ? $this->isFinished() : false,
            ],

            'rundown' => $this->whenLoaded('scheduleItems', fn() =>
                $this->scheduleItems->map(fn($item) => [
                    'start'       => $item->start,
                    'end'         => $item->end,
                    'title'       => $item->title,
                    'description' => $item->description,
                ])->values()
            , $this->schedule_json),

            'participants_count' => isset($this->registrations_count)
                ? (int) $this->registrations_count
                : null,

            // Only when loaded (e.g. authenticated user context)
            'is_registered' => isset($this->is_registered) ? (bool) $this->is_registered : null,
            'is_saved'       => isset($this->is_saved)      ? (bool) $this->is_saved      : null,

            'material_status' => $this->material_status,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
