<?php

namespace App\Services;

use Throwable;
use App\Helpers\SlugHelper;
use App\Models\Page;
use App\Models\StaffCategory;
use App\Models\User;
use App\Models\UserPagePosition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * HEMIS dan xodimning barcha lavozimlarini sync qilish.
 *
 * HemisAuthController (login) va EmployeeProfile (profil yangilash)
 * tomonidan umumiy ishlatiladi.
 */
class HemisPositionSyncService
{
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

    // employeeType.code → StaffCategory ma'lumotlari
    private const CATEGORY_MAP = [
        '12' => ['title_uz' => "Professor-o'qituvchi tarkibi", 'order' => 1],
        '11' => ['title_uz' => "Ma'muriyat",                   'order' => 2],
        '10' => ['title_uz' => 'Xodimlar',                     'order' => 3],
        '13' => ['title_uz' => 'Yordamchi xodimlar',           'order' => 4],
        '14' => ['title_uz' => "Xizmat ko'rsatuvchi xodimlar", 'order' => 5],
    ];

    // HEMIS structureType.code → Page.page_type
    private const DEPT_TYPE_MAP = [
        '11' => 'faculty',
        '12' => 'department',
        '13' => 'section',
        '14' => 'boshqarma',
        '15' => 'center',
    ];

    public function __construct(private HemisApiService $api) {}

    /**
     * Xodimning barcha lavozimlarini HEMIS dan olib sync qiladi.
     *
     * @param  User         $user        Sync qilinadigan foydalanuvchi
     * @param  string|null  $hemisLogin  OAuth raw data dagi login (tabel raqami)
     * @return bool  Muvaffaqiyatli sync bo'ldimi
     */
    public function sync(User $user, ?string $hemisLogin = null): bool
    {
        try {
            // UUID bo'yicha qidirish (eng ishonchli)
            $uuid      = $user->hemis_uuid;
            $positions = $uuid ? $this->api->fetchEmployeePositionsByUuid($uuid) : collect();

            // UUID ishlamasa — hemis_id ni sinab ko'rish
            if ($positions->isEmpty() && $user->hemis_id) {
                $positions = $this->api->fetchEmployeePositions($user->hemis_id);
            }

            // hemis_employee_id bo'yicha ham sinab ko'rish
            if ($positions->isEmpty() && $user->hemis_employee_id) {
                $positions = $this->api->fetchEmployeePositions($user->hemis_employee_id);
            }

            if ($positions->isEmpty()) {
                Log::info('HemisPositionSyncService: no data from API', [
                    'user_id'           => $user->id,
                    'hemis_uuid'        => $user->hemis_uuid,
                    'hemis_id'          => $user->hemis_id,
                    'hemis_employee_id' => $user->hemis_employee_id,
                ]);
                return false;
            }

            // Faqat shu xodimga tegishli yozuvlar
            $positions = $positions->filter(function ($emp) use ($user, $hemisLogin) {
                if ($user->hemis_uuid && ! empty($emp['uuid'])) {
                    return $emp['uuid'] === $user->hemis_uuid;
                }
                if ($hemisLogin && ! empty($emp['login'])) {
                    return $emp['login'] === $hemisLogin;
                }
                if ($user->hemis_employee_id) {
                    return (string) ($emp['id'] ?? '') === (string) $user->hemis_employee_id;
                }
                return false;
            });

            if ($positions->isEmpty()) {
                Log::warning('HemisPositionSyncService: no records after identity filter', [
                    'user_id'           => $user->id,
                    'hemis_uuid'        => $user->hemis_uuid,
                    'hemis_employee_id' => $user->hemis_employee_id,
                    'hemis_login'       => $hemisLogin,
                ]);
                return false;
            }

            // Faqat aktiv lavozimlari (11=ishlamoqda, 12=ta'tilda)
            $active = $positions->filter(
                fn($emp) => in_array($emp['employeeStatus']['code'] ?? 0, [11, 12])
            );

            if ($active->isEmpty()) {
                return false;
            }

            $this->writePositions($user, $active);
            return true;

        } catch (Throwable $e) {
            Log::error('HemisPositionSyncService: sync failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Xodim lavozimlarini user_page_positions ga yozadi.
     * Avval eski yozuvlar o'chiriladi — to'liq yangilash.
     */
    private function writePositions(User $user, Collection $active): void
    {
        UserPagePosition::where('user_id', $user->id)->delete();

        $sorted = $active->sortBy(function ($emp) {
            return self::POSITION_ORDER[$emp['staffPosition']['name'] ?? ''] ?? 99;
        })->values();

        $isPrimary     = true;
        $pageCache     = [];
        $categoryCache = [];

        foreach ($sorted as $emp) {
            $deptData = $emp['department'] ?? null;
            if (! $deptData || ! isset($deptData['id'])) {
                continue;
            }

            $deptId = $deptData['id'];

            if (! array_key_exists($deptId, $pageCache)) {
                $pageCache[$deptId] = $this->resolveOrCreatePage($deptData);
            }
            $page = $pageCache[$deptId];

            if (! $page) {
                $isPrimary = false;
                continue;
            }

            $typeCode = (string) ($emp['employeeType']['code'] ?? '10');
            $catKey   = $page->id . ':' . $typeCode;

            if (! array_key_exists($catKey, $categoryCache)) {
                $categoryCache[$catKey] = $this->resolveOrCreateCategory($page->id, $typeCode);
            }
            $category = $categoryCache[$catKey];

            $positionName  = $emp['staffPosition']['name'] ?? null;
            $positionOrder = self::POSITION_ORDER[$positionName ?? ''] ?? 99;

            UserPagePosition::updateOrCreate(
                ['user_id' => $user->id, 'page_id' => $page->id],
                [
                    'staff_category_id'        => $category?->id,
                    'position_uz'              => $positionName,
                    'position_ru'              => $positionName,
                    'position_en'              => $positionName,
                    'position_order'           => $positionOrder,
                    'employment_form'          => $emp['employmentForm']['name'] ?? null,
                    'hemis_employee_type_code' => $typeCode,
                    'hemis_position_id'        => (string) ($emp['id'] ?? ''),
                    'is_primary'               => $isPrimary,
                ]
            );

            if ($isPrimary) {
                $user->department_page_id = $page->id;
                $user->staff_category_id  = $category?->id;
                $user->position_uz        = $positionName;
                $user->position_ru        = $positionName;
                $user->position_en        = $positionName;
                $user->position_order     = $positionOrder;
                $user->employment_form    = $emp['employmentForm']['name'] ?? $user->employment_form;
                $user->academic_degree    = $emp['academicDegree']['name'] ?? $user->academic_degree;
                $user->academic_rank      = $emp['academicRank']['name']   ?? $user->academic_rank;
                $user->name               = $emp['full_name']              ?? $user->name;

                if (isset($emp['id'])) {
                    $user->hemis_employee_id = (string) $emp['id'];
                }

                $user->save();
                $isPrimary = false;
            }
        }
    }

    private function resolveOrCreatePage(array $dept): ?Page
    {
        $hemsDeptId = $dept['id'];

        $page = Page::where('hemis_id', $hemsDeptId)->first();
        if ($page) {
            return $page;
        }

        $typeCode = (string) ($dept['structureType']['code'] ?? '');
        $pageType = self::DEPT_TYPE_MAP[$typeCode] ?? null;

        if (! $pageType) {
            return null;
        }

        $name = $dept['name'] ?? "Noma'lum bo'lim";

        try {
            $page = Page::create([
                'hemis_id'  => $hemsDeptId,
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

            Log::info('HemisPositionSyncService: auto-created Page', [
                'hemis_id'  => $hemsDeptId,
                'name'      => $name,
                'page_type' => $pageType,
            ]);

            return $page;
        } catch (Throwable $e) {
            Log::error('HemisPositionSyncService: failed to auto-create Page', [
                'hemis_id' => $hemsDeptId,
                'name'     => $name,
                'error'    => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function resolveOrCreateCategory(int $pageId, string $typeCode): ?StaffCategory
    {
        $catDef = self::CATEGORY_MAP[$typeCode] ?? null;
        if (! $catDef) {
            return null;
        }

        return StaffCategory::firstOrCreate(
            [
                'page_id'                  => $pageId,
                'hemis_employee_type_code' => $typeCode,
            ],
            [
                'title_uz' => $catDef['title_uz'],
                'title_ru' => $catDef['title_uz'],
                'title_en' => $catDef['title_uz'],
                'order'    => $catDef['order'],
            ]
        );
    }
}
