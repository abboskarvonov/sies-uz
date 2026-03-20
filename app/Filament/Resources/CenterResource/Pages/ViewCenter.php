<?php

namespace App\Filament\Resources\CenterResource\Pages;

use App\Filament\Resources\CenterResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewCenter extends ViewRecord
{
    protected static string $resource = CenterResource::class;

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
