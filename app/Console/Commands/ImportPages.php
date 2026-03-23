<?php

namespace App\Console\Commands;

use App\Models\Menu;
use App\Models\Submenu;
use App\Models\Multimenu;
use Exception;
use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:pages';
    protected $description = 'Import pages from old database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Importing pages...');

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('pages')->truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $menuIdMap = Menu::pluck('id', 'old_id')->toArray();
        $submenuIdMap = Submenu::pluck('id', 'old_id')->toArray();
        $multimenuIdMap = Multimenu::pluck('id', 'old_id')->toArray();

        $oldSubmenuMenuMap = DB::connection('mysql_old')
            ->table('sub_menu')
            ->pluck('menu_id', 'id')
            ->toArray();

        $oldMultimenuSubmenuMap = DB::connection('mysql_old')
            ->table('multi_menu')
            ->pluck('sub_menu_id', 'id')
            ->toArray();

        $oldPages = DB::connection('mysql_old')->table('pages')->get();

        foreach ($oldPages as $oldPage) {
            $oldId = (int) $oldPage->id;
            $exists = Page::where('old_id', $oldId)->exists();

            $this->info("Checking page id {$oldId}: " . ($exists ? 'exists' : 'not exists'));
            if ($exists) {
                $this->info("Skipping page id {$oldId} - already imported");
                continue;
            }
            try {
                $this->info("Importing page id {$oldPage->id} - {$oldPage->name_uz}");

                $pageTypeMap = [
                    1 => 'default',
                    2 => 'blog',
                    3 => 'faculty',
                    4 => 'center',
                    5 => 'department',
                    6 => 'section',
                ];

                // Sluglarni tayyorlaymiz (helperga ajratish mumkin)
                $slugUz = $this->generateUniqueSlug(Page::class, 'slug_uz', $oldPage->name_uz);
                $slugRu = $this->generateUniqueSlug(Page::class, 'slug_ru', $oldPage->name_ru);
                $slugEn = $this->generateUniqueSlug(Page::class, 'slug_en', $oldPage->name_en);

                // Yangi ID larni aniqlaymiz
                $newMultimenuId = $multimenuIdMap[$oldPage->multi_menu_id] ?? null;
                $newSubmenuId = $submenuIdMap[$oldPage->sub_menu_id] ?? null;
                $newMenuId = null;

                if ($oldPage->multi_menu_id && isset($oldMultimenuSubmenuMap[$oldPage->multi_menu_id])) {
                    $oldSubmenuId = $oldMultimenuSubmenuMap[$oldPage->multi_menu_id];
                    $newSubmenuId = $submenuIdMap[$oldSubmenuId] ?? null;
                    $oldMenuId = $oldSubmenuMenuMap[$oldSubmenuId] ?? null;
                    $newMenuId = $menuIdMap[$oldMenuId] ?? null;
                } elseif ($oldPage->sub_menu_id && isset($oldSubmenuMenuMap[$oldPage->sub_menu_id])) {
                    $oldMenuId = $oldSubmenuMenuMap[$oldPage->sub_menu_id];
                    $newMenuId = $menuIdMap[$oldMenuId] ?? null;
                } elseif ($oldPage->menu_id) {
                    $newMenuId = $menuIdMap[$oldPage->menu_id] ?? null;
                }

                // Rasmlar
                $rawPaths = explode('**', $oldPage->more_imgs_url ?? '');
                $filteredPaths = array_filter($rawPaths, function ($path) {
                    $path = trim($path);
                    return $path !== '' && $path !== 'pages/' && !str_ends_with($path, '/');
                });
                $imagePaths = array_map(fn($path) => 'pages/' . basename($path), $filteredPaths);

                Page::updateOrCreate(
                    ['old_id' => (int)$oldPage->id],
                    [
                        'old_id'        => $oldPage->id,
                        'menu_id'       => $newMenuId,
                        'submenu_id'    => $newSubmenuId,
                        'multimenu_id'  => $newMultimenuId,

                        'title_uz' => $oldPage->name_uz ?? '',
                        'title_ru' => $oldPage->name_ru ?? '',
                        'title_en' => $oldPage->name_en ?? '',

                        'content_uz' => $oldPage->content_uz ?? '',
                        'content_ru' => $oldPage->content_ru ?? '',
                        'content_en' => $oldPage->content_en ?? '',

                        'slug_uz' => $oldPage->link ?? $slugUz,
                        'slug_ru' => $slugRu,
                        'slug_en' => $slugEn,

                        'page_type' => $pageTypeMap[$oldPage->type] ?? 'default',
                        'status' => 'active',

                        'date' => $oldPage->date ?? '',

                        'activity' => $oldPage->activity == 1 ? 1 : 0,

                        'views' => $oldPage->view ?? 0,

                        'image' => $oldPage->img ? 'pages/' . basename($oldPage->img) : null,
                        'images' => json_encode($imagePaths),

                        'order' => 0,

                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            } catch (Exception $e) {
                $this->error("Error importing page id {$oldPage->id}: " . $e->getMessage());
                continue;
            }
        }

        $this->info('Pages imported successfully!');
    }

    private function generateUniqueSlug($model, $field, $string)
    {
        $baseSlug = Str::slug($string ?? '');
        $slug = $baseSlug ?: (string) Str::uuid();
        $counter = 1;
        while ($model::where($field, $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        return $slug;
    }
}
