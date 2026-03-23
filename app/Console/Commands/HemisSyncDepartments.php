<?php

namespace App\Console\Commands;

use App\Helpers\SlugHelper;
use App\Models\Page;
use App\Services\HemisApiService;
use Illuminate\Console\Command;

class HemisSyncDepartments extends Command
{
    protected $signature   = 'hemis:sync-departments {--dry-run : O\'zgartirishsiz faqat ko\'rsatish}';
    protected $description = 'HEMIS dan kafedra, fakultet, bo\'lim, markazlarni Pages jadvaliga sync qilish';

    // structureType.code → page_type
    private const TYPE_MAP = [
        '11' => 'faculty',
        '12' => 'department',
        '13' => 'section',
        '14' => 'boshqarma',
        '15' => 'center',
    ];

    public function handle(HemisApiService $api): int
    {
        $this->info('HEMIS departments yuklanmoqda...');

        $items = $api->fetchAll('data/department-list');

        if ($items->isEmpty()) {
            $this->error('API dan ma\'lumot kelmadi. HEMIS_API_TOKEN ni tekshiring.');
            return self::FAILURE;
        }

        $this->info("Jami {$items->count()} ta tuzilma topildi.");

        $created = 0;
        $updated = 0;
        $skipped = 0;

        // Avval barcha itemlarni qayta ishlash (parent bog'lanish uchun 2 pass kerak)
        $mapped = $items->map(fn($item) => $this->resolveItem($item))
                        ->filter(); // null larni olib tashlaymiz

        // 1-pass: Barcha sahifalarni create/update
        foreach ($mapped as $item) {
            if ($this->option('dry-run')) {
                $this->line("  [DRY] {$item['page_type']} | {$item['hemis_id']} | {$item['title_uz']}");
                continue;
            }

            $page = Page::where('hemis_id', $item['hemis_id'])->first();

            if ($page) {
                $page->update(['title_uz' => $item['title_uz']]);
                $updated++;
            } else {
                $slugBase = $item['title_uz'];
                Page::create([
                    'hemis_id'  => $item['hemis_id'],
                    'title_uz'  => $item['title_uz'],
                    'title_ru'  => $item['title_uz'],
                    'title_en'  => $item['title_uz'],
                    'page_type' => $item['page_type'],
                    'status'    => 'active',
                    'date'      => now()->toDateString(),
                    'slug_uz'   => SlugHelper::generateUniqueSlug(Page::class, 'slug_uz', $slugBase),
                    'slug_ru'   => SlugHelper::generateUniqueSlug(Page::class, 'slug_ru', $slugBase),
                    'slug_en'   => SlugHelper::generateUniqueSlug(Page::class, 'slug_en', $slugBase),
                ]);
                $created++;
            }
        }

        // 2-pass: parent bog'lanish (kafedra → fakultet)
        if (! $this->option('dry-run')) {
            $this->info('Parent munosabatlar o\'rnatilmoqda...');
            $this->syncParents($items);
        }

        $this->info("Yaratildi: {$created} | Yangilandi: {$updated} | O'tkazildi: {$skipped}");
        return self::SUCCESS;
    }

    private function resolveItem(array $item): ?array
    {
        $typeCode = (string) ($item['structureType']['code'] ?? '');

        if (! isset(self::TYPE_MAP[$typeCode])) {
            // 10=Boshqa, 16=Rektorat — o'tkazib yuboriladi
            return null;
        }

        return [
            'hemis_id'  => $item['id'],
            'title_uz'  => $item['name'],
            'page_type' => self::TYPE_MAP[$typeCode],
            'parent_id' => $item['parent'],   // HEMIS parent id (int|null)
        ];
    }

    private function syncParents(iterable $items): void
    {
        foreach ($items as $item) {
            $parentHemisId = $item['parent'];
            if (! $parentHemisId) {
                continue;
            }

            $page       = Page::where('hemis_id', $item['id'])->first();
            $parentPage = Page::where('hemis_id', $parentHemisId)->first();

            if ($page && $parentPage) {
                // Multimenu orqali bog'lash emas, faqat metadata uchun;
                // Hozircha log qilamiz — keyinchalik frontendda ishlatiladi
                $this->line("  parent: [{$parentPage->title_uz}] → [{$page->title_uz}]");
            }
        }
    }
}
