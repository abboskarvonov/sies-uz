<?php

namespace App\Filament\Resources\MultimenuResource\Pages;

use App\Filament\Resources\MultimenuResource;
use App\Models\Multimenu;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditMultimenu extends EditRecord
{
    protected static string $resource = MultimenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug_uz'] = $this->generateUniqueSlug($data['title_uz'], 'slug_uz');
        $data['slug_ru'] = $this->generateUniqueSlug($data['title_ru'], 'slug_ru');
        $data['slug_en'] = $this->generateUniqueSlug($data['title_en'], 'slug_en');

        return $data;
    }

    protected function generateUniqueSlug($title, $column)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $i = 1;

        while (Multimenu::where($column, $slug)->exists()) {
            $slug = $originalSlug . '-' . $i++;
        }

        return $slug;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
