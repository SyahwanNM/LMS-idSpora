<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseModuleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'course_id' => (int) $this->course_id,
            'order_no' => (int) ($this->order_no ?? 0),
            'title' => (string) ($this->title ?? ''),
            'description' => $this->description,
            'type' => (string) ($this->type ?? 'video'),
            'is_free' => (bool) ($this->is_free ?? false),
            'preview_pages' => (int) ($this->preview_pages ?? 0),
            'duration' => (int) ($this->duration ?? 0),
        ];
    }
}
