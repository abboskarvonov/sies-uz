<?php

namespace App\Filament\Resources\SubmenuResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\SubmenuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubmenus extends ListRecords
{
    protected static string $resource = SubmenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
