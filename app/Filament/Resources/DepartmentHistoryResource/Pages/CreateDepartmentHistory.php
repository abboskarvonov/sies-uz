<?php

namespace App\Filament\Resources\DepartmentHistoryResource\Pages;

use App\Filament\Resources\DepartmentHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartmentHistory extends CreateRecord
{
    protected static string $resource = DepartmentHistoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
