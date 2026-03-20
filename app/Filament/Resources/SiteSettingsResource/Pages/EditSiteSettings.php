<?php

namespace App\Filament\Resources\SiteSettingsResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\SiteSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSiteSettings extends EditRecord
{
    protected static string $resource = SiteSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
