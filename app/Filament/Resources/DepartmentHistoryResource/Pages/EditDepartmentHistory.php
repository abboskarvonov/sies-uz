<?php

namespace App\Filament\Resources\DepartmentHistoryResource\Pages;

use App\Filament\Resources\DepartmentHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDepartmentHistory extends EditRecord
{
    protected static string $resource = DepartmentHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
