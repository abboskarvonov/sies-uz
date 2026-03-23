<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\PageFile;
use App\Models\Page;

class ImportPageFiles extends Command
{
    protected $signature = 'import:page-files';
    protected $description = 'Eski files jadvalidan page_files jadvaliga ma\'lumotlarni ko\'chirish (mapping bilan)';

    public function handle()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('page_files')->truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Fayllarni import qilish boshlandi...');

        $oldFiles = DB::connection('mysql_old')->table('files')->get();
        $count = 0;

        foreach ($oldFiles as $oldFile) {
            // Yangi page_id ni topamiz
            $newPage = Page::where('old_id', $oldFile->pages_id)->first();

            if (! $newPage) {
                $this->warn("Page topilmadi (old_id: {$oldFile->pages_id}), fayl ID {$oldFile->id} o'tkazib yuborildi");
                continue;
            }

            // Fayl nomini tozalash
            $fileName = basename($oldFile->file);
            $newPath = "pages/files/{$fileName}";

            try {
                PageFile::updateOrCreate([
                    'page_id' => $newPage->id,
                    'name' => $oldFile->name_uz ?? 'Nomsiz fayl',
                    'file' => $newPath,
                    'created_at' => $oldFile->created_at ?? now(),
                    'updated_at' => $oldFile->updated_at ?? now(),
                ]);
                $count++;
            } catch (Exception $e) {
                $this->error("Error importing file ID {$oldFile->id}: " . $e->getMessage());
            }
        }

        $this->info("Import yakunlandi! Jami {$count} ta fayl ko'chirildi.");
    }
}
