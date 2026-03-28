<?php

namespace App\Http\Controllers;

use Throwable;
use App\Helpers\SlugHelper;
use App\Models\Page;
use App\Models\StaffCategory;
use App\Models\User;
use App\Models\UserPagePosition;
use App\Services\HemisApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Laravel\Socialite\Facades\Socialite;

class HemisAuthController extends Controller
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
    // HemisSyncDepartments::TYPE_MAP bilan bir xil
    private const DEPT_TYPE_MAP = [
        '11' => 'faculty',
        '12' => 'department',
        '13' => 'section',
        '14' => 'boshqarma',
        '15' => 'center',
    ];

    // ─── Employee ────────────────────────────────────────────────────

    public function redirectEmployee(): RedirectResponse
    {
        return Socialite::driver('hemis-employee')->redirect();
    }

    public function callbackEmployee(): RedirectResponse
    {
        try {
            $hemisUser = Socialite::driver('hemis-employee')->user();
        } catch (Throwable $e) {
            Log::error('HEMIS employee OAuth error', ['error' => $e->getMessage()]);
            return redirect()->route('login')
                ->withErrors(['hemis' => "HEMIS orqali kirishda xatolik yuz berdi. Qaytadan urinib ko'ring."]);
        }

        $user = $this->findOrCreateUser($hemisUser, 'employee');

        // Login dan keyin HEMIS API dan lavozimlarini sync qilish.
        // Agar bo'lim/kafedra Page da yo'q bo'lsa avtomatik yaratiladi.
        $this->syncPositions($user, $hemisUser);

        Auth::login($user, remember: true);

        return redirect()->intended(route('employee.profile'));
    }

    // ─── Student ─────────────────────────────────────────────────────

    public function redirectStudent(): RedirectResponse
    {
        return Socialite::driver('hemis-student')->redirect();
    }

    public function callbackStudent(): RedirectResponse
    {
        try {
            $hemisUser = Socialite::driver('hemis-student')->user();
        } catch (Throwable $e) {
            Log::error('HEMIS student OAuth error', ['error' => $e->getMessage()]);
            return redirect()->route('login')
                ->withErrors(['hemis' => "HEMIS orqali kirishda xatolik yuz berdi. Qaytadan urinib ko'ring."]);
        }

        $user = $this->findOrCreateUser($hemisUser, 'student');

        Auth::login($user, remember: true);

        return redirect()->intended('/');
    }

    // ─── Shared: user topish / yaratish ──────────────────────────────

    private function findOrCreateUser($hemisUser, string $type): User
    {
        $hemisId    = (string) $hemisUser->getId();
        $hemisUuid  = $hemisUser->getRaw()['uuid'] ?? null;
        $hemisEmail = $hemisUser->getEmail() ?: null;
        $hemisName  = $hemisUser->getName();

        // 1. hemis_id bo'yicha qidirish
        $user = User::where('hemis_id', $hemisId)->first();

        // 2. Topilmasa — email bo'yicha qidirish (qo'lda ro'yxatdan o'tgan bo'lishi mumkin)
        if (! $user && $hemisEmail) {
            $user = User::where('email', $hemisEmail)->first();
        }

        if ($user) {
            $user->hemis_id   = $hemisId;
            $user->hemis_uuid = $hemisUuid ?? $user->hemis_uuid;
            $user->hemis_type = $type;
            $user->name       = $hemisName ?? $user->name;

            // HEMIS orqali kirgan — email verifikatsiya shart emas
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
            }

            $user->save();
            return $user;
        }

        // 3. Yangi foydalanuvchi yaratish
        return User::create([
            'hemis_id'           => $hemisId,
            'hemis_uuid'         => $hemisUuid,
            'hemis_type'         => $type,
            'name'               => $hemisName ?? 'HEMIS User',
            'email'              => $hemisEmail,
            'password'           => null,
            'profile_photo_path' => null,
            'email_verified_at'  => now(),
        ]);
    }

    // ─── Positions sync ──────────────────────────────────────────────

    /**
     * Employee login da HEMIS API dan barcha lavozimlarini olib sync qiladi.
     * Xato bo'lsa login to'xtamaydi — faqat log yoziladi.
     */
    private function syncPositions(User $user, $hemisUser): void
    {
        try {
            $api = app(HemisApiService::class);

            // UUID bo'yicha qidirish (eng ishonchli — barcha lavozimlari)
            $uuid      = $user->hemis_uuid;
            $positions = $uuid ? $api->fetchEmployeePositionsByUuid($uuid) : collect();

            // UUID ishlamasa — hemis_id ni employee_id sifatida sinab ko'rish
            if ($positions->isEmpty()) {
                $positions = $api->fetchEmployeePositions($user->hemis_id);
            }

            if ($positions->isEmpty()) {
                Log::info('HEMIS positions: no data for user', [
                    'user_id'  => $user->id,
                    'hemis_id' => $user->hemis_id,
                ]);
                return;
            }

            // Faqat shu xodimga tegishli yozuvlar (API ba'zan barcha xodimlarni qaytaradi)
            $positions = $positions->filter(function ($emp) use ($user) {
                if ($user->hemis_uuid && ! empty($emp['uuid'])) {
                    return $emp['uuid'] === $user->hemis_uuid;
                }
                return (string) ($emp['id'] ?? '') === (string) $user->hemis_id;
            });

            if ($positions->isEmpty()) {
                Log::warning('HEMIS positions: no matching records for user after identity filter', [
                    'user_id'     => $user->id,
                    'hemis_id'    => $user->hemis_id,
                    'hemis_uuid'  => $user->hemis_uuid,
                ]);
                return;
            }

            // Faqat aktiv lavozimlari (11=ishlamoqda, 12=ta'tilda)
            $active = $positions->filter(
                fn($emp) => in_array($emp['employeeStatus']['code'] ?? 0, [11, 12])
            );

            if ($active->isEmpty()) {
                return;
            }

            $this->writePositions($user, $active);

        } catch (Throwable $e) {
            Log::error('HEMIS positions sync error on login', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * HEMIS lavozimlar ro'yxatini user_page_positions ga yozadi.
     *
     * Asosiy yangilik: Page topilmasa HEMIS ma'lumotlaridan avtomatik yaratiladi.
     * Bu xodimni admin sync qilmagan bo'lim/kafedra xodimlar ro'yxatiga ham
     * avtomatik qo'shish imkonini beradi.
     */
    private function writePositions(User $user, Collection $active): void
    {
        // Position order bo'yicha saralash — eng yuqori lavozim birinchi (asosiy)
        $sorted = $active->sortBy(function ($emp) {
            $pos = $emp['staffPosition']['name'] ?? '';
            return self::POSITION_ORDER[$pos] ?? 99;
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

            // Page topish yoki avtomatik yaratish
            if (! array_key_exists($deptId, $pageCache)) {
                $pageCache[$deptId] = $this->resolveOrCreatePage($deptData);
            }
            $page = $pageCache[$deptId];

            if (! $page) {
                // Tur qo'llab-quvvatlanmaydi (rektorat va h.k.) — o'tkazib yuborish
                $isPrimary = false;
                continue;
            }

            // StaffCategory topish yoki yaratish
            $typeCode = (string) ($emp['employeeType']['code'] ?? '10');
            $catKey   = $page->id . ':' . $typeCode;

            if (! array_key_exists($catKey, $categoryCache)) {
                $categoryCache[$catKey] = $this->resolveOrCreateCategory($page->id, $typeCode);
            }
            $category = $categoryCache[$catKey];

            $positionName  = $emp['staffPosition']['name'] ?? null;
            $positionOrder = self::POSITION_ORDER[$positionName ?? ''] ?? 99;

            // user_page_positions: user_id + page_id = UNIQUE (upsert)
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

            // Asosiy lavozim — users jadvaliga ham yozish (backward-compat)
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

                // hemis_employee_id — employee-list API dagi record id
                if (isset($emp['id'])) {
                    $user->hemis_employee_id = (string) $emp['id'];
                }

                $user->save();
                $isPrimary = false;
            }
        }
    }

    /**
     * Page ni hemis_id bo'yicha topadi; topilmasa HEMIS ma'lumotlaridan yaratadi.
     *
     * Avtomatik yaratilgan Page:
     *  - menu_id/submenu_id/multimenu_id = null (admin keyinroq to'ldiradi)
     *  - status = 'active'
     *  - page_type = HEMIS structureType dan aniqlanadi
     *
     * @param  array  $dept  HEMIS department obyekti
     */
    private function resolveOrCreatePage(array $dept): ?Page
    {
        $hemsDeptId = $dept['id'];

        // Avval DB dan qidirish
        $page = Page::where('hemis_id', $hemsDeptId)->first();
        if ($page) {
            return $page;
        }

        // HEMIS tuzilma turi → page_type
        $typeCode = (string) ($dept['structureType']['code'] ?? '');
        $pageType = self::DEPT_TYPE_MAP[$typeCode] ?? null;

        if (! $pageType) {
            // Rektorat (10), Boshqa (16) — tizimda alohida Page sifatida saqlanmaydi
            return null;
        }

        $name = $dept['name'] ?? 'Noma\'lum bo\'lim';

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

            Log::info('HEMIS auth: auto-created Page', [
                'hemis_id'  => $hemsDeptId,
                'name'      => $name,
                'page_type' => $pageType,
                'page_id'   => $page->id,
            ]);

            return $page;
        } catch (Throwable $e) {
            Log::error('HEMIS auth: failed to auto-create Page', [
                'hemis_id' => $hemsDeptId,
                'name'     => $name,
                'error'    => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * StaffCategory ni topadi yoki yaratadi.
     */
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
