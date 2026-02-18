<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteStatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'campus_area' => $this->campus_area,
            'green_area' => $this->green_area,
            'faculties' => $this->faculties,
            'departments' => $this->departments,
            'centers' => $this->centers,
            'employees' => $this->employees,
            'leadership' => $this->leadership,
            'scientific' => $this->scientific,
            'technical' => $this->technical,
            'students' => $this->students,
            'male_students' => $this->male_students,
            'female_students' => $this->female_students,
            'teachers' => $this->teachers,
            'dsi' => $this->dsi,
            'phd_teachers' => $this->phd_teachers,
            'professors' => $this->professors,
            'books' => $this->books,
            'textbooks' => $this->textbooks,
            'study' => $this->study,
            'methodological' => $this->methodological,
            'monograph' => $this->monograph,
        ];
    }
}
