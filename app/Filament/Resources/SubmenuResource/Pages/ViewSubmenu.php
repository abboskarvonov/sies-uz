<?php

namespace App\Filament\Resources\SubmenuResource\Pages;

use App\Filament\Resources\SubmenuResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewSubmenu extends ViewRecord
{
    protected static string $resource = SubmenuResource::class;

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
