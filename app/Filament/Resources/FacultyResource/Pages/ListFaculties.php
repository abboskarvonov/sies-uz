<?php

namespace App\Filament\Resources\FacultyResource\Pages;

use App\Filament\Actions\AssignMenuToFacultiesAction;
use App\Filament\Actions\SyncHemisDepartmentsAction;
use App\Filament\Resources\FacultyResource;
use Filament\Resources\Pages\ListRecords;

class ListFaculties extends ListRecords
{
    protected static string $resource = FacultyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SyncHemisDepartmentsAction::make(),
            AssignMenuToFacultiesAction::make()->forPageType('faculty', 'fakultet'),
        ];
    }
}
