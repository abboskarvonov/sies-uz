<?php

namespace App\Http\Resources\Api\V1\Admin;

use App\Http\Traits\Api\HasImageUrls;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    use HasImageUrls;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title_uz' => $this->title_uz,
            'title_ru' => $this->title_ru,
            'title_en' => $this->title_en,
            'slug_uz' => $this->slug_uz,
            'slug_ru' => $this->slug_ru,
            'slug_en' => $this->slug_en,
            'content_uz' => $this->content_uz,
            'content_ru' => $this->content_ru,
            'content_en' => $this->content_en,
            'image' => $this->imageUrl($this->image),
            'images' => $this->imageUrls($this->images),
            'date' => $this->date?->toDateString(),
            'status' => $this->status,
            'page_type' => $this->page_type,
            'activity' => $this->activity,
            'views' => $this->views,
            'order' => $this->order,
            'menu_id' => $this->menu_id,
            'submenu_id' => $this->submenu_id,
            'multimenu_id' => $this->multimenu_id,
            'tag' => $this->tag,
            'tags' => $this->whenLoaded('tags', fn () => $this->tags->pluck('id')),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'updated_by' => new UserResource($this->whenLoaded('updatedBy')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
