<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Helpers\SlugHelper;
use App\Models\Page;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $titleUz = $data['title_uz'];
        $titleRu = !empty($data['title_ru']) ? $data['title_ru'] : $titleUz;
        $titleEn = !empty($data['title_en']) ? $data['title_en'] : $titleUz;

        $data['slug_uz'] = SlugHelper::generateUniqueSlug(Page::class, 'slug_uz', $titleUz);
        $data['slug_ru'] = SlugHelper::generateUniqueSlug(Page::class, 'slug_ru', $titleRu);
        $data['slug_en'] = SlugHelper::generateUniqueSlug(Page::class, 'slug_en', $titleEn);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
