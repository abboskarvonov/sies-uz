<?php

namespace App\Filament\Resources\MultimenuResource\Pages;

use App\Filament\Resources\MultimenuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMultimenus extends ListRecords
{
    protected static string $resource = MultimenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
