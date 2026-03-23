<?php

namespace App\Console\Commands;

use Throwable;
use App\Models\Page;
use App\Models\StaffCategory;
use App\Models\User;
use App\Services\HemisApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HemisSyncEmployees extends Command
{
    protected $signature   = 'hemis:sync-employees
                                {--skip-photos : Rasmlarni yuklab olmaslik}
                                {--dry-run : O\'zgartirishsiz faqat ko\'rsatish}';
    protected $description = 'HEMIS dan xodimlarni Users jadvaliga sync qilish';

    // staffPosition.name → position_order
    private const POSITION_ORDER = [
        'Rektor'                                          => 1,
        'O\'quv ishlari bo\'yicha birinchi prorektor'     => 2,
        'Yoshlar bilan ishlash bo\'yicha prorektor'       => 3,
        'Rektor maslahatchisi'                            => 4,
        'Rektor yordamchisi'                              => 5,
        'Dekan'                                           => 6,
        'Dekan muovini'                                   => 7,
        'Yoshlar bilan ishlash bo\'yicha dekan o\'rinbosari' => 8,
        'Kafedra mudiri'                                  => 9,
        'Professor'                                       => 10,
        'Dotsent'                                         => 11,
        'Katta o\'qituvchi'                               => 12,
        'Assistent'                                       => 13,
        'Stajer-o\'qituvchi'                              => 14,
        'Tyutor'                                          => 15,
        'Bo\'lim boshlig\'i'                              => 16,
        'Bo\'lim mudiri'                                  => 17,
        'Boshqarma boshlig\'i'                            => 18,
    ];

    // employeeType.code → [title_uz, order]
    private const CATEGORY_MAP = [
        '12' => ['title_uz' => 'Professor-o\'qituvchi tarkibi', 'order' => 1],
        '11' => ['title_uz' => 'Ma\'muriyat',                   'order' => 2],
        '10' => ['title_uz' => 'Xodimlar',                      'order' => 3],
        '13' => ['title_uz' => 'Yordamchi xodimlar',            'order' => 4],
        '14' => ['title_uz' => 'Xizmat ko\'rsatuvchi xodimlar', 'order' => 5],
    ];

    // Kesh: hemis_dept_id → Page (takroriy DB so'rovlarni oldini olish)
    private array $pageCache = [];

    // Kesh: "page_id:type_code" → StaffCategory
    private array $categoryCache = [];

    public function handle(HemisApiService $api): int
    {
        $this->info('HEMIS xodimlar yuklanmoqda...');

        $employees = $api->fetchAll('data/employee-list', ['type' => 11]);

        if ($employees->isEmpty()) {
            $this->error('API dan ma\'lumot kelmadi. HEMIS_API_TOKEN ni tekshiring.');
            return self::FAILURE;
        }

        $this->info("Jami {$employees->count()} ta xodim topildi.");

        $bar     = $this->output->createProgressBar($employees->count());
        $created = 0;
        $updated = 0;

        foreach ($employees as $emp) {
            $bar->advance();

            // Bo'shagan xodimlarni o'tkazib yuborish
            // employeeStatus: 11=Ishlamoqda, 12=Ta'tilda → sync; 14=Bo'shagan → skip
            if (! in_array($emp['employeeStatus']['code'] ?? 0, [11, 12])) {
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("\n  [DRY] {$emp['id']} | {$emp['full_name']}");
                continue;
            }

            $user = $this->upsertUser($emp);

            if ($user->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("Yaratildi: {$created} | Yangilandi: {$updated}");

        return self::SUCCESS;
    }

    private function upsertUser(array $emp): User
    {
        $hemisId      = (string) $emp['id'];
        $deptPage     = $this->resolveDepPage($emp['department'] ?? null);
        $category     = $this->resolveCategory($emp, $deptPage);
        $positionOrder = $this->resolvePositionOrder($emp);
        $email        = $emp['email'] ?? null ?: null;

        // 1. hemis_employee_id bo'yicha qidirish (asosiy)
        $user = User::where('hemis_employee_id', $hemisId)->first();

        // 2. Topilmasa — email bo'yicha qidirish
        if (! $user && $email) {
            $user = User::where('email', $email)->first();
        }

        // 3. Yangi user
        if (! $user) {
            $user = new User();
        }

        $isNew = ! $user->exists;

        // HEMIS dan keladigan ma'lumotlar (har safar yangilanadi)
        $user->hemis_employee_id  = $hemisId;
        $user->hemis_uuid         = $emp['uuid'] ?? null;
        $user->hemis_type         = 'employee';
        $user->name               = $emp['full_name'] ?? 'HEMIS Xodim';
        $user->position_uz        = $emp['staffPosition']['name'] ?? null;
        $user->academic_degree    = $emp['academicDegree']['name'] ?? null;
        $user->academic_rank      = $emp['academicRank']['name'] ?? null;
        $user->employment_form    = $emp['employmentForm']['name'] ?? null;
        $user->position_order     = $positionOrder;
        $user->department_page_id = $deptPage?->id;
        $user->staff_category_id  = $category?->id;
        $user->email_verified_at  = $user->email_verified_at ?? now();

        // Email — faqat bo'sh bo'lsagina yoziladi (mavjud email o'zgartirilmaydi)
        if (! $user->email && $email) {
            $user->email = $email;
        }

        // Foto — faqat yangi user uchun yoki yo'q bo'lsa
        if (! $this->option('skip-photos')) {
            if ($isNew || ! $user->profile_photo_path) {
                $avatarUrl = $emp['image'] ?? null;
                if ($avatarUrl) {
                    $photoPath = $this->downloadPhoto($avatarUrl, $hemisId);
                    if ($photoPath) {
                        $user->profile_photo_path = $photoPath;
                    }
                }
            }
        }

        $user->save();
        return $user;
    }

    private function resolveDepPage(?array $dept): ?Page
    {
        if (! $dept || ! isset($dept['id'])) {
            return null;
        }

        $hemDeptId = $dept['id'];

        if (! array_key_exists($hemDeptId, $this->pageCache)) {
            $this->pageCache[$hemDeptId] = Page::where('hemis_id', $hemDeptId)->first();
        }

        return $this->pageCache[$hemDeptId];
    }

    private function resolveCategory(array $emp, ?Page $page): ?StaffCategory
    {
        if (! $page) {
            return null;
        }

        $typeCode = (string) ($emp['employeeType']['code'] ?? '10');

        if (! isset(self::CATEGORY_MAP[$typeCode])) {
            return null;
        }

        $cacheKey = $page->id . ':' . $typeCode;

        if (! array_key_exists($cacheKey, $this->categoryCache)) {
            $catData = self::CATEGORY_MAP[$typeCode];

            $this->categoryCache[$cacheKey] = StaffCategory::firstOrCreate(
                [
                    'page_id'                  => $page->id,
                    'hemis_employee_type_code'  => $typeCode,
                ],
                [
                    'title_uz' => $catData['title_uz'],
                    'title_ru' => $catData['title_uz'],
                    'title_en' => $catData['title_uz'],
                    'order'    => $catData['order'],
                ]
            );
        }

        return $this->categoryCache[$cacheKey];
    }

    private function resolvePositionOrder(array $emp): int
    {
        $position = $emp['staffPosition']['name'] ?? '';
        return self::POSITION_ORDER[$position] ?? 99;
    }

    private function downloadPhoto(string $url, string $hemis_id): ?string
    {
        try {
            $content = file_get_contents($url);
            if ($content === false || strlen($content) < 100) {
                return null;
            }

            $path = 'profile-photos/' . $hemis_id . '.jpg';
            Storage::disk('public')->put($path, $content);
            return $path;
        } catch (Throwable $e) {
            Log::warning('HEMIS photo download failed', [
                'hemis_id' => $hemis_id,
                'url'      => $url,
                'error'    => $e->getMessage(),
            ]);
            return null;
        }
    }
}
