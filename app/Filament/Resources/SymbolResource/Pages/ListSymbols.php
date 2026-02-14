<?php

namespace App\Filament\Resources\SymbolResource\Pages;

use App\Filament\Resources\SymbolResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSymbols extends ListRecords
{
    protected static string $resource = SymbolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
