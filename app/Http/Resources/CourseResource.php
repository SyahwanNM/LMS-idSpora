<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $media = (string) ($this->media ?? '');
        $thumb = (string) ($this->card_thumbnail ?? '');

        $hasDiscount = method_exists($this->resource, 'hasDiscount') ? $this->hasDiscount() : false;
        $priceFinal  = $hasDiscount
            ? (float) ($this->discounted_price ?? $this->price ?? 0)
            : (float) ($this->price ?? 0);

        return [
            'id'               => (int) $this->id,
            'name'             => (string) ($this->name ?? ''),
            'description'      => $this->description,
            'level'            => (string) ($this->level ?? ''),
            'status'           => (string) ($this->status ?? ''),
            'duration'         => (int) ($this->duration ?? 0),
            'free_access_mode' => $this->free_access_mode,

            'pricing' => [
                'price_original'      => (float) ($this->price ?? 0),
                'price_final'         => $priceFinal,
                'discount_percent'    => (int) ($this->discount_percent ?? 0),
                'discount_start'      => $this->discount_start?->toDateString(),
                'discount_end'        => $this->discount_end?->toDateString(),
                'is_discounted'       => $hasDiscount,
                'is_free'             => $priceFinal <= 0,
            ],

            'media' => [
                'type'          => $this->media_type,
                'path'          => $media !== '' ? $media : null,
                'url'           => $media !== '' ? Storage::disk('public')->url($media) : null,
                'thumbnail_url' => $thumb !== '' ? Storage::disk('public')->url($thumb) : null,
            ],

            'category' => $this->whenLoaded('category', fn() => [
                'id'   => (int) ($this->category->id ?? 0),
                'name' => (string) ($this->category->name ?? ''),
            ]),

            'modules_count'     => isset($this->modules_count) ? (int) $this->modules_count : null,
            'enrollments_count' => isset($this->enrollments_count) ? (int) $this->enrollments_count : null,
            'modules'           => CourseModuleResource::collection($this->whenLoaded('modules')),

            'rating' => [
                'average' => isset($this->reviews_avg_rating)
                    ? round((float) $this->reviews_avg_rating, 1)
                    : null,
                'count' => isset($this->reviews_count) ? (int) $this->reviews_count : null,
            ],

            // Authenticated user context
            'is_enrolled'      => isset($this->is_enrolled)      ? (bool) $this->is_enrolled      : null,
            'is_saved'         => isset($this->is_saved)         ? (bool) $this->is_saved         : null,
            'progress_percent' => isset($this->progress_percent) ? (int)  $this->progress_percent : null,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
