<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventRegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'event' => $this->whenLoaded('event', function () {
                return new EventResource($this->event);
            }),
            'event_id' => $this->event_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'registration_code' => $this->registration_code,
            'total_price' => (int) $this->total_price,
            'payment_url' => $this->payment_url,
            'attendance_status' => $this->attendance_status,
            'attended_at' => optional($this->attended_at)->toISOString(),
            'certificate_number' => $this->certificate_number,
            'certificate_issued_at' => optional($this->certificate_issued_at)->toISOString(),
            'feedback_text' => $this->feedback_text,
            'feedback_submitted_at' => optional($this->feedback_submitted_at)->toISOString(),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
