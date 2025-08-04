<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewPage extends ViewRecord
{
    protected static string $resource = PageResource::class;

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
