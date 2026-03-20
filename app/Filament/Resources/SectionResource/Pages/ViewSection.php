<?php

namespace App\Filament\Resources\SectionResource\Pages;

use App\Filament\Resources\SectionResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewSection extends ViewRecord
{
    protected static string $resource = SectionResource::class;

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
