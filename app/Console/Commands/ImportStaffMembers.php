<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\StaffMember;
use App\Models\Page;

class ImportStaffMembers extends Command
{
    protected $signature = 'import:staff-members';
    protected $description = 'Eski employee jadvalidan staff_members jadvaliga ma\'lumotlarni ko\'chirish (mapping bilan)';

    public function handle()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('staff_members')->truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Staff members import qilinmoqda...');

        $oldEmployees = DB::connection('mysql_old')->table('employee')->get();
        $count = 0;

        foreach ($oldEmployees as $oldEmp) {
            // Yangi page_id ni topamiz
            $newPage = Page::where('old_id', $oldEmp->pages_id)->first();

            if (! $newPage) {
                $this->warn("Page topilmadi (old_id: {$oldEmp->pages_id}), employee ID {$oldEmp->id}  o'tkazib yuborildi");
                continue;
            }

            // Rasm nomini tozalash
            $fileName = basename($oldEmp->img);
            $newPath = "staff_members/{$fileName}";

            try {
                StaffMember::updateOrCreate([
                    'page_id' => $newPage->id,

                    'name_uz' => $oldEmp->name_uz ?? '',
                    'name_ru' => $oldEmp->name_ru ?? '',
                    'name_en' => $oldEmp->name_en ?? '',

                    'position_uz' => $oldEmp->position_uz ?? '',
                    'position_ru' => $oldEmp->position_ru ?? '',
                    'position_en' => $oldEmp->position_en ?? '',

                    'content_uz' => $oldEmp->content_uz ?? '',
                    'content_ru' => $oldEmp->content_ru ?? '',
                    'content_en' => $oldEmp->content_en ?? '',

                    'image' => $newPath,

                    'created_at' => $oldEmp->created_at ?? now(),
                    'updated_at' => $oldEmp->updated_at ?? now(),
                ]);
                $count++;
            } catch (Exception $e) {
                $this->error("Error importing employee ID {$oldEmp->id}: " . $e->getMessage());
            }
        }

        $this->info("Import yakunlandi! Jami {$count} ta xodim ko'chirildi.");
    }
}
