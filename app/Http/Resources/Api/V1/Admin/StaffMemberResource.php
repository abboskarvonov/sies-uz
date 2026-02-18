<?php

namespace App\Http\Resources\Api\V1\Admin;

use App\Http\Traits\Api\HasImageUrls;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffMemberResource extends JsonResource
{
    use HasImageUrls;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_uz' => $this->name_uz,
            'name_ru' => $this->name_ru,
            'name_en' => $this->name_en,
            'position_uz' => $this->position_uz,
            'position_ru' => $this->position_ru,
            'position_en' => $this->position_en,
            'content_uz' => $this->content_uz,
            'content_ru' => $this->content_ru,
            'content_en' => $this->content_en,
            'image' => $this->imageUrl($this->image),
            'page_id' => $this->page_id,
            'staff_category_id' => $this->staff_category_id,
            'user_id' => $this->user_id,
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'updated_by' => new UserResource($this->whenLoaded('updatedBy')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
