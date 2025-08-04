<?php

namespace App\Console\Commands;

use App\Models\Submenu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportSubmenus extends Command
{
    protected $signature = 'import:submenus';
    protected $description = 'Import submenus from old database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Importing submenus...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('submenus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // Yangi bazadagi menus mappingi
        $menuIdMap = \App\Models\Menu::pluck('id', 'old_id')->toArray();

        $oldSubmenus = DB::connection('mysql_old')->table('sub_menu')->get();

        foreach ($oldSubmenus as $oldSubmenu) {

            try {
                $this->info("Importing submenu id {$oldSubmenu->id} - {$oldSubmenu->name_uz}");

                // Slug yasash funksiyasini bitta helperga ajratish yaxshi, hozircha shu yerda yozamiz
                $slugUz = $this->generateUniqueSlug(Submenu::class, 'slug_uz', $oldSubmenu->name_uz);
                $slugRu = $this->generateUniqueSlug(Submenu::class, 'slug_ru', $oldSubmenu->name_ru);
                $slugEn = $this->generateUniqueSlug(Submenu::class, 'slug_en', $oldSubmenu->name_en);

                // Import qilamiz
                Submenu::updateOrCreate(
                    ['id' => $oldSubmenu->id],
                    [
                        'old_id' => $oldSubmenu->id,
                        'title_uz' => $oldSubmenu->name_uz ?? '',
                        'title_ru' => $oldSubmenu->name_ru ?? '',
                        'title_en' => $oldSubmenu->name_en ?? '',

                        'slug_uz' => $slugUz,
                        'slug_ru' => $slugRu,
                        'slug_en' => $slugEn,

                        'menu_id' => $menuIdMap[$oldSubmenu->menu_id] ?? 1,

                        'link' => '', // Bo‘sh qoladi
                        'type' => 'multimenu', // Default multimenu

                        'status' => 1, // Default ON
                        'order' => 0, // Default 0

                        'created_by' => 1,
                        'updated_by' => 1,

                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            } catch (\Exception $e) {
                $this->error("Error importing submenu id {$oldSubmenu->id}: " . $e->getMessage());
                continue;
            }
        }

        $this->info('Submenus imported successfully!');
    }

    private function generateUniqueSlug($model, $field, $string)
    {
        $baseSlug = Str::slug($string ?? '');
        $slug = $baseSlug;
        $counter = 1;

        while ($model::where($field, $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug ?: (string) Str::uuid();
    }
}
