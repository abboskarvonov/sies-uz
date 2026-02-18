<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Traits\Api\HasImageUrls;
use App\Http\Traits\Api\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffMemberResource extends JsonResource
{
    use HasImageUrls, HasLocalizedFields;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->localizedField($this->resource, 'name'),
            'position' => $this->localizedField($this->resource, 'position'),
            'image' => $this->imageUrl($this->image),
        ];
    }
}
