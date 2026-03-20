<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewDepartment extends ViewRecord
{
    protected static string $resource = DepartmentResource::class;

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
