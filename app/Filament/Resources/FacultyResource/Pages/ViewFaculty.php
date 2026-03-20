<?php

namespace App\Filament\Resources\FacultyResource\Pages;

use App\Filament\Resources\FacultyResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewFaculty extends ViewRecord
{
    protected static string $resource = FacultyResource::class;

    public function getTitle(): string|Htmlable
    {
        $record = $this->getRecord();
        $field = 'title_' . app()->getLocale();

        return $record->{$field} ?? "Ko'rish";
    }

    protected function getActions(): array
    {
        return [];
    }
}
