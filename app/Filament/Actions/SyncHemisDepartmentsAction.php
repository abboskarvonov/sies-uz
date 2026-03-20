<?php

namespace App\Filament\Actions;

use App\Helpers\SlugHelper;
use App\Models\Page;
use App\Services\HemisApiService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class SyncHemisDepartmentsAction extends Action
{
    // structureType.code → page_type
    private const TYPE_MAP = [
        '11' => 'faculty',
        '12' => 'department',
        '13' => 'section',
        '14' => 'boshqarma',
        '15' => 'center',
    ];

    public static function getDefaultName(): ?string
    {
        return 'syncHemisDepartments';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label("HEMIS dan yangilash")
            ->icon('heroicon-o-arrow-path')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading("HEMIS dan tuzilmalarni yangilash")
            ->modalDescription(
                "HEMIS API dan barcha fakultet, kafedra, bo'lim va markazlar ma'lumotlari yuklanadi. " .
                "Mavjud yozuvlar nomi yangilanadi, yangilari avtomatik yaratiladi."
            )
            ->modalSubmitActionLabel('Boshlash')
            ->action(fn () => $this->runSync());
    }

    private function runSync(): void
    {
        $api   = app(HemisApiService::class);
        $items = $api->fetchAll('data/department-list');

        if ($items->isEmpty()) {
            Notification::make()
                ->title("Ma'lumot kelmadi")
                ->body("HEMIS API dan javob kelmadi. Token yoki ulanishni tekshiring.")
                ->danger()
                ->send();
            return;
        }

        $created = 0;
        $updated = 0;

        // 1-pass: barcha sahifalarni yaratish/yangilash
        foreach ($items as $item) {
            $resolved = $this->resolveItem($item);
            if (! $resolved) {
                continue;
            }

            $page = Page::where('hemis_id', $resolved['hemis_id'])->first();

            if ($page) {
                $page->update(['title_uz' => $resolved['title_uz']]);
                $updated++;
            } else {
                $base = $resolved['title_uz'];
                Page::create([
                    'hemis_id'  => $resolved['hemis_id'],
                    'title_uz'  => $base,
                    'title_ru'  => $base,
                    'title_en'  => $base,
                    'page_type' => $resolved['page_type'],
                    'status'    => 'active',
                    'date'      => now()->toDateString(),
                    'slug_uz'   => SlugHelper::generateUniqueSlug(Page::class, 'slug_uz', $base),
                    'slug_ru'   => SlugHelper::generateUniqueSlug(Page::class, 'slug_ru', $base),
                    'slug_en'   => SlugHelper::generateUniqueSlug(Page::class, 'slug_en', $base),
                ]);
                $created++;
            }
        }

        // 2-pass: parent bog'lanishlarni o'rnatish (bo'lim → markaz, kafedra → fakultet)
        $parentsLinked = 0;
        foreach ($items as $item) {
            $parentHemisId = $item['parent'] ?? null;
            if (! $parentHemisId) {
                continue;
            }

            $page       = Page::where('hemis_id', (string) $item['id'])->first();
            $parentPage = Page::where('hemis_id', (string) $parentHemisId)->first();

            if ($page && $parentPage && $page->parent_page_id !== $parentPage->id) {
                $page->update(['parent_page_id' => $parentPage->id]);
                $parentsLinked++;
            }
        }

        Notification::make()
            ->title('HEMIS sync tugadi')
            ->body("Yaratildi: {$created} ta | Yangilandi: {$updated} ta | Parent bog'landi: {$parentsLinked} ta")
            ->success()
            ->send();
    }

    private function resolveItem(array $item): ?array
    {
        $typeCode = (string) ($item['structureType']['code'] ?? '');

        if (! isset(self::TYPE_MAP[$typeCode])) {
            // 10=Boshqa, 16=Rektorat va boshqalar — o'tkazib yuboriladi
            return null;
        }

        return [
            'hemis_id'  => (string) $item['id'],
            'title_uz'  => $item['name'],
            'page_type' => self::TYPE_MAP[$typeCode],
        ];
    }
}
