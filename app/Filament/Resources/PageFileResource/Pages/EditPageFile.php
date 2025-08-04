<?php

namespace App\Filament\Resources\PageFileResource\Pages;

use App\Filament\Resources\PageFileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPageFile extends EditRecord
{
    protected static string $resource = PageFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
