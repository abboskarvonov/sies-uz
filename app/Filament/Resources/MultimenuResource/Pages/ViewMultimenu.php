<?php

namespace App\Filament\Resources\MultimenuResource\Pages;

use App\Filament\Resources\MultimenuResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewMultimenu extends ViewRecord
{
    protected static string $resource = MultimenuResource::class;

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
