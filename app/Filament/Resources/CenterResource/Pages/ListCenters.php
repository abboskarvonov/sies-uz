<?php

namespace App\Filament\Resources\CenterResource\Pages;

use App\Filament\Actions\AssignMenuToFacultiesAction;
use App\Filament\Actions\SyncHemisDepartmentsAction;
use App\Filament\Resources\CenterResource;
use Filament\Resources\Pages\ListRecords;

class ListCenters extends ListRecords
{
    protected static string $resource = CenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SyncHemisDepartmentsAction::make(),
            AssignMenuToFacultiesAction::make()->forPageType('center', 'markaz'),
        ];
    }
}
