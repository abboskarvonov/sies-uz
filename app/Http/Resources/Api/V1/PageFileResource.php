<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Traits\Api\HasImageUrls;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageFileResource extends JsonResource
{
    use HasImageUrls;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->imageUrl($this->file),
        ];
    }
}
