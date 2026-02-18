<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Traits\Api\HasImageUrls;
use App\Http\Traits\Api\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MultimenuResource extends JsonResource
{
    use HasImageUrls, HasLocalizedFields;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->localizedField($this->resource, 'title'),
            'slug' => $this->localizedField($this->resource, 'slug'),
            'link' => $this->link,
            'image' => $this->imageUrl($this->image),
            'order' => $this->order,
        ];
    }
}
