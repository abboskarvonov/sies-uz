<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'log_name' => $this->log_name,
            'description' => $this->description,
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,
            'event' => $this->event,
            'properties' => $this->properties,
            'causer' => $this->when($this->causer, [
                'id' => $this->causer?->id,
                'name' => $this->causer?->name,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
