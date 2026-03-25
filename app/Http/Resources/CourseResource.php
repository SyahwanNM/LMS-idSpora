<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CourseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $media = (string) ($this->media ?? '');
        $thumb = (string) ($this->card_thumbnail ?? '');

        return [
            'id' => (int) $this->id,
            'name' => (string) ($this->name ?? ''),
            'description' => $this->description,
            'level' => (string) ($this->level ?? ''),
            'status' => (string) ($this->status ?? ''),
            'price' => (int) ($this->price ?? 0),
            'duration' => (int) ($this->duration ?? 0),
            'free_access_mode' => $this->free_access_mode,

            'discount_percent' => $this->discount_percent,
            'discount_start' => $this->discount_start?->toDateString(),
            'discount_end' => $this->discount_end?->toDateString(),

            'media_type' => $this->media_type,
            'media_path' => $media !== '' ? $media : null,
            'media_url' => $media !== '' ? Storage::disk('public')->url($media) : null,

            'card_thumbnail_path' => $thumb !== '' ? $thumb : null,
            'card_thumbnail_url' => $thumb !== '' ? Storage::disk('public')->url($thumb) : null,

            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => (int) ($this->category->id ?? 0),
                    'name' => (string) ($this->category->name ?? ''),
                ];
            }),

            'modules_count' => isset($this->modules_count) ? (int) $this->modules_count : null,
            'modules' => CourseModuleResource::collection($this->whenLoaded('modules')),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
