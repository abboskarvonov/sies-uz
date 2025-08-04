<?php

namespace App\Filament\Resources\DepartmentHistoryResource\Pages;

use App\Filament\Resources\DepartmentHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepartmentHistories extends ListRecords
{
    protected static string $resource = DepartmentHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
