<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Menu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportMenus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:menus';
    protected $description = 'Eski bazadan menus jadvalini import qilish';

    /**
     * Execute the console command.
     */


    public function handle()
    {
        $this->info('Importing menus...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('menus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // Eski bazadan menus olib kelamiz
        $oldMenus = DB::connection('mysql_old')->table('menu')->get();

        foreach ($oldMenus as $oldMenu) {
            try {
                $this->info("Importing menu id {$oldMenu->id} - {$oldMenu->name_uz}");

                $slugUz = $this->generateUniqueSlug(Menu::class, 'slug_uz', $oldMenu->name_uz);
                $slugRu = $this->generateUniqueSlug(Menu::class, 'slug_ru', $oldMenu->name_ru);
                $slugEn = $this->generateUniqueSlug(Menu::class, 'slug_en', $oldMenu->name_en);

                Menu::updateOrCreate(
                    ['old_id' => $oldMenu->id],
                    [
                        'old_id' => $oldMenu->id,
                        'title_uz' => $oldMenu->name_uz ?? '',
                        'title_ru' => $oldMenu->name_ru ?? '',
                        'title_en' => $oldMenu->name_en ?? '',

                        'slug_uz' => $slugUz,
                        'slug_ru' => $slugRu,
                        'slug_en' => $slugEn,

                        'link' => '',
                        'menu_type' => 'dropdown',
                        'position' => 'header',
                        'order' => 0,
                        'image' => null,
                        'created_by' => 1,
                        'updated_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            } catch (Exception $e) {
                $this->error("Error importing menu id {$oldMenu->id}: " . $e->getMessage());
                continue;
            }
        }

        $this->info('Menus imported successfully!');
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

        return $slug;
    }
}
