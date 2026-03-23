<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\DepartmentHistory;
use App\Models\Page;

class ImportDepartmentHistories extends Command
{
    protected $signature = 'import:department-histories';
    protected $description = 'Eski department_history jadvalidan department_histories jadvaliga ma\'lumotlarni ko\'chirish (mapping bilan)';

    public function handle()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('department_histories')->truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Department histories import qilinmoqda...');

        $oldHistories = DB::connection('mysql_old')->table('department_history')->get();
        $count = 0;

        foreach ($oldHistories as $oldHistory) {
            // Yangi department_id ni topamiz (pages.old_id bo‘yicha)
            $newPage = Page::where('old_id', $oldHistory->pages_id)->first();

            if (! $newPage) {
                $this->warn("Page topilmadi (old_id: {$oldHistory->pages_id}), history ID {$oldHistory->id} o'tkazib yuborildi");
                continue;
            }

            $this->info("Importing department history id {$oldHistory->id}");

            try {
                DepartmentHistory::updateOrCreate([
                    'department_id' => $newPage->id,
                    'content_uz' => $oldHistory->content_uz ?? '',
                    'content_ru' => $oldHistory->content_ru ?? '',
                    'content_en' => $oldHistory->content_en ?? '',
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => $oldHistory->created_at ?? '',
                    'updated_at' => $oldHistory->updated_at ?? '',
                ]);
                $count++;
            } catch (Exception $e) {
                $this->error("Error importing department_history ID {$oldHistory->id}: " . $e->getMessage());
            }
        }

        $this->info("Import yakunlandi! Jami {$count} ta yozuv ko'chirildi.");
    }
}
