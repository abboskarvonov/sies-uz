<?php

namespace App\Console\Commands;

use App\Models\Menu;
use App\Models\Submenu;
use App\Models\Multimenu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportMultimenus extends Command
{
    protected $signature = 'import:multimenus';
    protected $description = 'Import multimenus from old database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Importing multimenus...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('multimenus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Yangi bazadagi mappinglar
        $newMenuIdMap = Menu::pluck('id', 'old_id')->toArray();
        $newSubmenuIdMap = Submenu::pluck('id', 'old_id')->toArray();

        // Eski submenu → menu mapping
        $oldSubmenuMenuMap = DB::connection('mysql_old')
            ->table('sub_menu')
            ->pluck('menu_id', 'id') // eski submenu.id => eski menu_id
            ->toArray();

        $oldMultimenus = DB::connection('mysql_old')->table('multi_menu')->get();

        foreach ($oldMultimenus as $oldMultimenu) {

            $slugUz = $this->generateUniqueSlug(Multimenu::class, 'slug_uz', $oldMultimenu->name_uz);
            $slugRu = $this->generateUniqueSlug(Multimenu::class, 'slug_ru', $oldMultimenu->name_ru);
            $slugEn = $this->generateUniqueSlug(Multimenu::class, 'slug_en', $oldMultimenu->name_en);

            // Yangi submenu va menu ID larni topamiz
            $newSubmenuId = $newSubmenuIdMap[$oldMultimenu->sub_menu_id] ?? null;
            $oldMenuId = $oldSubmenuMenuMap[$oldMultimenu->sub_menu_id] ?? null;
            $newMenuId = $newMenuIdMap[$oldMenuId] ?? null;

            // Import qilamiz
            Multimenu::updateOrCreate(
                ['id' => $oldMultimenu->id],
                [
                    'old_id'   => $oldMultimenu->id,
                    'title_uz' => $oldMultimenu->name_uz ?? '',
                    'title_ru' => $oldMultimenu->name_ru ?? '',
                    'title_en' => $oldMultimenu->name_en ?? '',

                    'slug_uz' => $slugUz,
                    'slug_ru' => $slugRu,
                    'slug_en' => $slugEn,

                    'menu_id'    => $newMenuId,
                    'submenu_id' => $newSubmenuId,

                    'link' => '', // Bo‘sh qoladi

                    'status' => 1, // Default ON
                    'order' => 0, // Default 0

                    'created_by' => 1,
                    'updated_by' => 1,

                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->info('Multimenus imported successfully!');
    }

    private function generateUniqueSlug($model, $field, $string)
    {
        $baseSlug = Str::slug($string ?? '');
        $slug = $baseSlug ?: (string) Str::uuid();
        $counter = 1;

        while ($model::where($field, $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
