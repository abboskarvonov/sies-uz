<?php

namespace App\Filament\Resources\BoshqarmaResource\Pages;

use App\Filament\Resources\BoshqarmaResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewBoshqarma extends ViewRecord
{
    protected static string $resource = BoshqarmaResource::class;

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
