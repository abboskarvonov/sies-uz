<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Helpers\SlugHelper;
use App\Models\Page;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $id = $this->record->id;

        // Bo'sh bo'lsa uz ni fallback sifatida ishlatamiz
        $titleUz = $data['title_uz'];
        $titleRu = !empty($data['title_ru']) ? $data['title_ru'] : $titleUz;
        $titleEn = !empty($data['title_en']) ? $data['title_en'] : $titleUz;

        $data['slug_uz'] = SlugHelper::generateUniqueSlug(Page::class, 'slug_uz', $titleUz, $id);
        $data['slug_ru'] = SlugHelper::generateUniqueSlug(Page::class, 'slug_ru', $titleRu, $id);
        $data['slug_en'] = SlugHelper::generateUniqueSlug(Page::class, 'slug_en', $titleEn, $id);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
