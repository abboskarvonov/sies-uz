<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Traits\Api\HasImageUrls;
use App\Http\Traits\Api\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PageListResource extends JsonResource
{
    use HasImageUrls, HasLocalizedFields;

    public function toArray(Request $request): array
    {
        $content = $this->localizedField($this->resource, 'content');

        return [
            'id' => $this->id,
            'title' => $this->localizedField($this->resource, 'title'),
            'slug' => $this->localizedField($this->resource, 'slug'),
            'excerpt' => $content ? Str::limit(strip_tags($content), 200) : null,
            'image' => $this->imageUrl($this->image),
            'date' => $this->date?->toDateString(),
            'views' => $this->views,
            'page_type' => $this->page_type,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
