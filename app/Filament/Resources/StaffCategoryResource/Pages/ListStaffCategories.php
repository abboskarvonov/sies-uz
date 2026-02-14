<?php

namespace App\Filament\Resources\StaffCategoryResource\Pages;

use App\Filament\Resources\StaffCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaffCategories extends ListRecords
{
    protected static string $resource = StaffCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
