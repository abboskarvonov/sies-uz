<?php

namespace App\Console\Commands;

use Throwable;
use App\Helpers\SlugHelper;
use App\Models\Page;
use App\Models\StaffCategory;
use App\Models\User;
use App\Models\UserPagePosition;
use App\Services\HemisApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * HEMIS dan barcha xodimlarni sync qilish.
 *
 * Bir xodim bir nechta lavozimda ishlashi mumkin (masalan, kafedra mudiri +
 * markaz boshlig'i). HEMIS API da ular bir xil UUID bilan, lekin turli
 * department va staffPosition bilan keladi.
 *
 * Strategiya:
 *  1. Barcha yozuvlarni UUID bo'yicha guruhlash.
 *  2. Har bir UUID = bitta User.
 *  3. User uchun birinchi (position_order eng kichik) yozuv — asosiy lavozim.
 *     → users.department_page_id, users.staff_category_id
 *  4. Barcha lavozimlari → user_page_positions.
 */
class HemisSyncEmployees extends Command
{
    protected $signature   = 'hemis:sync-employees
                                {--skip-photos : Rasmlarni yuklab olmaslik}
                                {--dry-run : O\'zgartirishsiz faqat ko\'rsatish}';
    protected $description = 'HEMIS dan xodimlarni Users jadvaliga sync qilish';

    // staffPosition.name → position_order
    private const POSITION_ORDER = [
        'Rektor'                                             => 1,
        "O'quv ishlari bo'yicha birinchi prorektor"          => 2,
        "Yoshlar bilan ishlash bo'yicha prorektor"           => 3,
        'Rektor maslahatchisi'                               => 4,
        'Rektor yordamchisi'                                 => 5,
        'Dekan'                                              => 6,
        'Dekan muovini'                                      => 7,
        "Yoshlar bilan ishlash bo'yicha dekan o'rinbosari"   => 8,
        'Kafedra mudiri'                                     => 9,
        'Professor'                                          => 10,
        'Dotsent'                                            => 11,
        "Katta o'qituvchi"                                   => 12,
        'Assistent'                                          => 13,
        "Stajer-o'qituvchi"                                  => 14,
        'Tyutor'                                             => 15,
        "Bo'lim boshlig'i"                                   => 16,
        "Bo'lim mudiri"                                      => 17,
        "Boshqarma boshlig'i"                                => 18,
    ];

    // HEMIS structureType.code → Page.page_type
    private const DEPT_TYPE_MAP = [
        '11' => 'faculty',
        '12' => 'department',
        '13' => 'section',
        '14' => 'boshqarma',
        '15' => 'center',
    ];

    // employeeType.code → [title_uz, order]
    private const CATEGORY_MAP = [
        '12' => ['title_uz' => "Professor-o'qituvchi tarkibi", 'order' => 1],
        '11' => ['title_uz' => "Ma'muriyat",                   'order' => 2],
        '10' => ['title_uz' => 'Xodimlar',                     'order' => 3],
        '13' => ['title_uz' => 'Yordamchi xodimlar',           'order' => 4],
        '14' => ['title_uz' => "Xizmat ko'rsatuvchi xodimlar", 'order' => 5],
    ];

    // Kesh: hemis_dept_id → Page|null
    private array $pageCache = [];

    // Kesh: "page_id:type_code" → StaffCategory|null
    private array $categoryCache = [];

    public function handle(HemisApiService $api): int
    {
        $this->info('HEMIS xodimlar yuklanmoqda...');

        $allRows = $api->fetchAll('data/employee-list', ['type' => 11]);

        if ($allRows->isEmpty()) {
            $this->error("API dan ma'lumot kelmadi. HEMIS_API_TOKEN ni tekshiring.");
            return self::FAILURE;
        }

        $this->info("Jami {$allRows->count()} ta yozuv topildi.");

        // Faqat aktiv xodimlar (11=ishlamoqda, 12=ta'tilda)
        $active = $allRows->filter(
            fn($emp) => in_array($emp['employeeStatus']['code'] ?? 0, [11, 12])
        );

        // Bitta xodim = bitta guruh. Kalit ustuvorligi:
        //   1. uuid  — HEMIS da shaxsning global identifikatori (eng ishonchli)
        //   2. login — tabel raqami / username (uuid yo'q bo'lsa)
        //   3. id    — faqat so'nggi chora (shu record ning ID si)
        // Maqsad: dekan + o'qituvchi kabi bir xodimning 2 yozuvini birlashtirish.
        $grouped = $active->groupBy(function ($emp) {
            if (! empty($emp['uuid'])) {
                return 'uuid:' . $emp['uuid'];
            }
            if (! empty($emp['login'])) {
                return 'login:' . $emp['login'];
            }
            return 'id:' . $emp['id'];
        });

        $this->info("Aktiv xodimlar: {$active->count()} yozuv, {$grouped->count()} ta noyob xodim.");

        if ($this->option('dry-run')) {
            foreach ($grouped as $uuid => $rows) {
                $primary = $this->pickPrimary($rows);
                $this->line("  [DRY] UUID:{$uuid} | {$primary['full_name']} | {$rows->count()} lavozim");
            }
            return self::SUCCESS;
        }

        $bar     = $this->output->createProgressBar($grouped->count());
        $created = 0;
        $updated = 0;

        foreach ($grouped as $uuid => $rows) {
            $bar->advance();

            $result = $this->upsertUser($rows);

            if ($result->wasRecentlyCreated) {
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

    /**
     * Bir xodimning barcha yozuvlari (lavozimlari) asosida User upsert qiladi,
     * so'ng barcha lavozimlari user_page_positions ga yoziladi.
     */
    private function upsertUser(Collection $rows): User
    {
        $primary = $this->pickPrimary($rows);
        $email   = $primary['email'] ?? null ?: null;
        $uuid    = $primary['uuid']  ?? null;

        $login = $primary['login'] ?? null ?: null;

        // User topish (ustuvorlik tartibi):
        //   1. hemis_uuid  — shaxsning global identifikatori
        //   2. hemis_employee_id — asosiy lavozimning ID si
        //   3. email       — qo'lda ro'yxatdan o'tgan bo'lishi mumkin
        // hemis_uuid bo'yicha birinchi qidiramiz — bir xodimning eski
        // yozuvi boshqa hemis_employee_id bilan saqlangan bo'lishi mumkin.
        $user = null;

        if ($uuid) {
            $user = User::where('hemis_uuid', $uuid)->first();
        }

        if (! $user) {
            $user = User::where('hemis_employee_id', (string) $primary['id'])->first();
        }

        if (! $user && $email) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $user = new User();
        }

        $isNew = ! $user->exists;

        // Asosiy lavozim ma'lumotlari
        $primaryPage     = $this->resolveDepPage($primary['department'] ?? null);
        $primaryCategory = $this->resolveCategory($primary, $primaryPage);
        $primaryOrder    = $this->resolvePositionOrder($primary);

        // HEMIS dan keladigan ma'lumotlar (har safar yangilanadi)
        $user->hemis_employee_id = (string) $primary['id'];
        $user->hemis_uuid        = $uuid;
        $user->hemis_type        = 'employee';
        $user->name              = $primary['full_name'] ?? 'HEMIS Xodim';
        $user->position_uz       = $primary['staffPosition']['name'] ?? null;
        $user->position_ru       = $primary['staffPosition']['name'] ?? null;
        $user->position_en       = $primary['staffPosition']['name'] ?? null;
        $user->academic_degree   = $primary['academicDegree']['name'] ?? null;
        $user->academic_rank     = $primary['academicRank']['name']   ?? null;
        $user->employment_form   = $primary['employmentForm']['name'] ?? null;
        $user->position_order    = $primaryOrder;
        $user->department_page_id = $primaryPage?->id;
        $user->staff_category_id  = $primaryCategory?->id;
        $user->email_verified_at  = $user->email_verified_at ?? now();

        // Email — faqat bo'sh bo'lsagina yoziladi
        if (! $user->email && $email) {
            $user->email = $email;
        }

        // Foto — faqat yangi user uchun yoki yo'q bo'lsa
        if (! $this->option('skip-photos')) {
            if ($isNew || ! $user->profile_photo_path) {
                $avatarUrl = $primary['image'] ?? null;
                if ($avatarUrl) {
                    $photoPath = $this->downloadPhoto($avatarUrl, (string) $primary['id']);
                    if ($photoPath) {
                        $user->profile_photo_path = $photoPath;
                    }
                }
            }
        }

        $user->save();

        // Barcha lavozimlari → user_page_positions
        $this->upsertPositions($user, $rows);

        return $user;
    }

    /**
     * Xodimning barcha lavozimlari (turli bo'lim/kafedralardagi)
     * user_page_positions jadvaliga upsert qilinadi.
     */
    private function upsertPositions(User $user, Collection $rows): void
    {
        $primary = $this->pickPrimary($rows);
        $primaryEmpId = (string) $primary['id'];

        foreach ($rows as $emp) {
            $page = $this->resolveDepPage($emp['department'] ?? null);
            if (! $page) {
                continue; // Bo'lim hali sync qilinmagan
            }

            $category     = $this->resolveCategory($emp, $page);
            $positionName = $emp['staffPosition']['name'] ?? null;
            $posOrder     = $this->resolvePositionOrder($emp);
            $typeCode     = (string) ($emp['employeeType']['code'] ?? '10');
            $empId        = (string) ($emp['id'] ?? '');

            UserPagePosition::updateOrCreate(
                ['user_id' => $user->id, 'page_id' => $page->id],
                [
                    'staff_category_id'        => $category?->id,
                    'position_uz'              => $positionName,
                    'position_ru'              => $positionName,
                    'position_en'              => $positionName,
                    'position_order'           => $posOrder,
                    'employment_form'          => $emp['employmentForm']['name'] ?? null,
                    'hemis_employee_type_code' => $typeCode,
                    'hemis_position_id'        => $empId,
                    'is_primary'               => ($empId === $primaryEmpId),
                ]
            );
        }
    }

    /**
     * Bir nechta lavozim ichidan asosiyini tanlash:
     * position_order eng kichigi (yuqori lavozim).
     */
    private function pickPrimary(Collection $rows): array
    {
        return $rows->sortBy(function ($emp) {
            $posName = $emp['staffPosition']['name'] ?? '';
            return self::POSITION_ORDER[$posName] ?? 99;
        })->first();
    }

    private function resolveDepPage(?array $dept): ?Page
    {
        if (! $dept || ! isset($dept['id'])) {
            return null;
        }

        $hemDeptId = $dept['id'];

        if (! array_key_exists($hemDeptId, $this->pageCache)) {
            $page = Page::where('hemis_id', $hemDeptId)->first();

            if (! $page) {
                $page = $this->autoCreatePage($dept);
            }

            $this->pageCache[$hemDeptId] = $page;
        }

        return $this->pageCache[$hemDeptId];
    }

    /**
     * HEMIS department ma'lumotlaridan Page avtomatik yaratadi.
     * menu_id/submenu_id/multimenu_id = null — admin keyinroq to'ldiradi.
     */
    private function autoCreatePage(array $dept): ?Page
    {
        $typeCode = (string) ($dept['structureType']['code'] ?? '');
        $pageType = self::DEPT_TYPE_MAP[$typeCode] ?? null;

        if (! $pageType) {
            return null; // Rektorat va h.k. — o'tkazib yuborish
        }

        $name = $dept['name'] ?? "Noma'lum bo'lim";

        try {
            $page = Page::create([
                'hemis_id'  => $dept['id'],
                'title_uz'  => $name,
                'title_ru'  => $name,
                'title_en'  => $name,
                'page_type' => $pageType,
                'status'    => 'active',
                'date'      => now()->toDateString(),
                'slug_uz'   => SlugHelper::generateUniqueSlug(Page::class, 'slug_uz', $name),
                'slug_ru'   => SlugHelper::generateUniqueSlug(Page::class, 'slug_ru', $name),
                'slug_en'   => SlugHelper::generateUniqueSlug(Page::class, 'slug_en', $name),
            ]);

            $this->line("\n  [AUTO PAGE] {$pageType} | {$name}");
            return $page;
        } catch (Throwable $e) {
            Log::error('HemisSyncEmployees: auto-create Page failed', [
                'hemis_id' => $dept['id'],
                'name'     => $name,
                'error'    => $e->getMessage(),
            ]);
            return null;
        }
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
                    'hemis_employee_type_code' => $typeCode,
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

    private function downloadPhoto(string $url, string $hemisId): ?string
    {
        try {
            $content = file_get_contents($url);
            if ($content === false || strlen($content) < 100) {
                return null;
            }

            $path = 'profile-photos/' . $hemisId . '.jpg';
            Storage::disk('public')->put($path, $content);
            return $path;
        } catch (Throwable $e) {
            Log::warning('HEMIS photo download failed', [
                'hemis_id' => $hemisId,
                'url'      => $url,
                'error'    => $e->getMessage(),
            ]);
            return null;
        }
    }
}
