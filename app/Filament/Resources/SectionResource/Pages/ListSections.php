<?php

namespace App\Filament\Resources\SectionResource\Pages;

use App\Filament\Actions\AssignMenuToFacultiesAction;
use App\Filament\Actions\SyncHemisDepartmentsAction;
use App\Filament\Resources\SectionResource;
use Filament\Resources\Pages\ListRecords;

class ListSections extends ListRecords
{
    protected static string $resource = SectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SyncHemisDepartmentsAction::make(),
            AssignMenuToFacultiesAction::make()->forPageType('section', "bo'lim"),
        ];
    }
}
