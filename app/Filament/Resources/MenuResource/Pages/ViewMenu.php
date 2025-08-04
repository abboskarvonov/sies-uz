<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewMenu extends ViewRecord
{
    protected static string $resource = MenuResource::class;

    public function getTitle(): string|Htmlable
    {
        $record = $this->getRecord();
        $locale = app()->getLocale();

        $field = 'title_' . $locale;

        return $record->{$field} ?? 'Ko‘rish';
    }

    protected function getActions(): array
    {
        return [];
    }
}
