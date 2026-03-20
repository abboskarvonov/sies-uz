<?php

namespace App\Filament\Resources\BoshqarmaResource\Pages;

use App\Filament\Actions\AssignMenuToFacultiesAction;
use App\Filament\Actions\SyncHemisDepartmentsAction;
use App\Filament\Resources\BoshqarmaResource;
use Filament\Resources\Pages\ListRecords;

class ListBoshqarmalar extends ListRecords
{
    protected static string $resource = BoshqarmaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SyncHemisDepartmentsAction::make(),
            AssignMenuToFacultiesAction::make()->forPageType('boshqarma', 'boshqarma'),
        ];
    }
}
