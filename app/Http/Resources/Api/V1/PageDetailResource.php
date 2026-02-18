<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Traits\Api\HasImageUrls;
use App\Http\Traits\Api\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageDetailResource extends JsonResource
{
    use HasImageUrls, HasLocalizedFields;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->localizedField($this->resource, 'title'),
            'slug' => $this->localizedField($this->resource, 'slug'),
            'content' => $this->localizedField($this->resource, 'content'),
            'image' => $this->imageUrl($this->image),
            'images' => $this->imageUrls($this->images),
            'date' => $this->date?->toDateString(),
            'views' => $this->views,
            'page_type' => $this->page_type,
            'activity' => $this->activity,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'files' => PageFileResource::collection($this->whenLoaded('files')),
            'staff_categories' => StaffCategoryResource::collection($this->whenLoaded('staffCategories')),
            'department_history' => new DepartmentHistoryResource($this->whenLoaded('departmentHistory')),
            'menu' => $this->when($this->relationLoaded('menu'), function () {
                return [
                    'id' => $this->menu->id,
                    'title' => $this->localizedField($this->menu, 'title'),
                    'slug' => $this->localizedField($this->menu, 'slug'),
                ];
            }),
            'submenu' => $this->when($this->relationLoaded('submenu'), function () {
                if (!$this->submenu) return null;
                return [
                    'id' => $this->submenu->id,
                    'title' => $this->localizedField($this->submenu, 'title'),
                    'slug' => $this->localizedField($this->submenu, 'slug'),
                ];
            }),
            'multimenu' => $this->when($this->relationLoaded('multimenu'), function () {
                if (!$this->multimenu) return null;
                return [
                    'id' => $this->multimenu->id,
                    'title' => $this->localizedField($this->multimenu, 'title'),
                    'slug' => $this->localizedField($this->multimenu, 'slug'),
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
