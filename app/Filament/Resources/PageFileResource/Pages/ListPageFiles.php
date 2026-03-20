<?php

namespace App\Filament\Resources\PageFileResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PageFileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPageFiles extends ListRecords
{
    protected static string $resource = PageFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
