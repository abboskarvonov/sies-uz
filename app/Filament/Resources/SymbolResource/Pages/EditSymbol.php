<?php

namespace App\Filament\Resources\SymbolResource\Pages;

use App\Filament\Resources\SymbolResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSymbol extends EditRecord
{
    protected static string $resource = SymbolResource::class;

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
