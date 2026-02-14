<?php

namespace App\Filament\Resources\StaffCategoryResource\Pages;

use App\Filament\Resources\StaffCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStaffCategory extends CreateRecord
{
    protected static string $resource = StaffCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
