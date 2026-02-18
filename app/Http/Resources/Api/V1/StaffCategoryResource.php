<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Traits\Api\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffCategoryResource extends JsonResource
{
    use HasLocalizedFields;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->localizedField($this->resource, 'title'),
            'staff_members' => StaffMemberResource::collection($this->whenLoaded('staffMembers')),
            'children' => StaffCategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
