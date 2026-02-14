<?php

namespace App\Filament\Resources\StaffCategoryResource\Pages;

use App\Filament\Resources\StaffCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaffCategory extends EditRecord
{
    protected static string $resource = StaffCategoryResource::class;

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
