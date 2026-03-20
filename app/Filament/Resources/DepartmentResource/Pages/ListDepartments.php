<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Actions\AssignMenuToFacultiesAction;
use App\Filament\Actions\SyncHemisDepartmentsAction;
use App\Filament\Resources\DepartmentResource;
use Filament\Resources\Pages\ListRecords;

class ListDepartments extends ListRecords
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SyncHemisDepartmentsAction::make(),
            AssignMenuToFacultiesAction::make()->forPageType('department', 'kafedra'),
        ];
    }
}
