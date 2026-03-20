<?php

namespace App\Filament\Resources\MenuResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug_uz'] = Str::slug($data['title_uz']);
        $data['slug_ru'] = Str::slug($data['title_ru']);
        $data['slug_en'] = Str::slug($data['title_en']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}