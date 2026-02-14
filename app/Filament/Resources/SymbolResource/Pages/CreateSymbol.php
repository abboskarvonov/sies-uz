<?php

namespace App\Filament\Resources\SymbolResource\Pages;

use App\Filament\Resources\SymbolResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSymbol extends CreateRecord
{
    protected static string $resource = SymbolResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
