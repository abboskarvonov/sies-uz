<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Traits\Api\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentHistoryResource extends JsonResource
{
    use HasLocalizedFields;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->localizedField($this->resource, 'content'),
        ];
    }
}
