<?php

namespace App\Filament\Actions;

use Filament\Forms\Components\Toggle;
use Throwable;
use App\Models\StaffCategory;
use App\Models\User;
use App\Services\HemisApiService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SyncHemisEmployeesAction extends Action
{
    // staffPosition.name → position_order
    private const POSITION_ORDER = [
        'Rektor'                                              => 1,
        "O'quv ishlari bo'yicha birinchi prorektor"           => 2,
        "Yoshlar bilan ishlash bo'yicha prorektor"            => 3,
        'Rektor maslahatchisi'                                => 4,
        'Rektor yordamchisi'                                  => 5,
        'Dekan'                                               => 6,
        'Dekan muovini'                                       => 7,
        "Yoshlar bilan ishlash bo'yicha dekan o'rinbosari"    => 8,
        'Kafedra mudiri'                                      => 9,
        'Professor'                                           => 10,
        'Dotsent'                                             => 11,
        "Katta o'qituvchi"                                    => 12,
        'Assistent'                                           => 13,
        "Stajer-o'qituvchi"                                   => 14,
        'Tyutor'                                              => 15,
        "Bo'lim boshlig'i"                                    => 16,
        "Bo'lim mudiri"                                       => 17,
        "Boshqarma boshlig'i"                                 => 18,
    ];


    public static function getDefaultName(): ?string
    {
        return 'syncHemisEmployees';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('HEMIS dan xodimlarni yuklash')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('warning')
            ->modalHeading('HEMIS dan xodimlarni yuklash')
            ->modalDescription(
                "Tizimda mavjud xodimlar faqat lavozim/bo'lim ma'lumotlari yangilanadi. " .
                "Ism, email, parol va rasm o'ZGARTIRILMAYDI."
            )
            ->schema([
                Toggle::make('dry_run')
                    ->label('Faqat tekshirish (hech narsa yozilmaydi)')
                    ->helperText('Yoqilsa sync qilinmaydi — faqat natija ko\'rsatiladi')
                    ->default(false),
            ])
            ->modalSubmitActionLabel('Boshlash')
            ->action(function ($record, array $data) {
                $this->runSync($record, $data['dry_run'] ?? false);
            });
    }

    private function runSync($page, bool $dryRun = false): void
    {
        if (empty($page->hemis_id)) {
            Notification::make()
                ->title('HEMIS ID mavjud emas')
                ->body("Bu sahifaga HEMIS ID biriktirilmagan. Avval hemis_id ni kiriting.")
                ->warning()
                ->persistent()
                ->send();
            return;
        }

        $api = app(HemisApiService::class);
        $all = $api->fetchAll('data/employee-list', ['type' => 11]);

        // Faqat shu bo'lim/kafedra/fakultet xodimlari
        // employeeStatus: 11=Ishlamoqda, 12=Ta'tilda → sync; 14=Bo'shagan → o'tkazib yuborish
        $employees = $all->filter(
            fn($emp) => isset($emp['department']['id']) &&
                        (string) $emp['department']['id'] === (string) $page->hemis_id &&
                        in_array($emp['employeeStatus']['code'] ?? 0, [11, 12])
        );

        if ($employees->isEmpty()) {
            // Debug: HEMIS dan kelayotgan noyob department ID larini ko'rsatish
            $deptIds = $all->pluck('department.id')->filter()->unique()->sort()->values()->take(20)->implode(', ');
            Notification::make()
                ->title('Xodimlar topilmadi')
                ->body(
                    "Sahifa hemis_id: [{$page->hemis_id}]\n" .
                    "HEMIS da mavjud department.id lar (birinchi 20 ta): {$deptIds}"
                )
                ->warning()
                ->persistent()
                ->send();
            return;
        }

        $newList   = [];   // [employee_id => name] — tizimda topilmagan, yangi yaratiladi
        $foundList = [];   // [employee_id => "HEMIS nomi (tizim: user nomi)"]

        foreach ($employees as $emp) {
            $employeeId = (string) $emp['id'];
            $name       = $emp['full_name'] ?? '—';

            $existing = User::where('hemis_employee_id', $employeeId)->first();

            if ($existing) {
                $foundList[$employeeId] = "{$name} → tizim: {$existing->name}";
            } else {
                $newList[$employeeId] = $name;
            }
        }

        // Dry-run: faqat natijani ko'rsatish
        if ($dryRun) {
            $body  = "Jami: {$employees->count()} ta xodim\n";
            $body .= "✅ Tizimda topildi (yangilanadi): " . count($foundList) . " ta\n";
            $body .= "🆕 Tizimda yo'q (yangi yaratiladi): " . count($newList) . " ta";

            if (count($foundList)) {
                $body .= "\n\n— Yangilanadi —";
                foreach (array_slice($foundList, 0, 8, true) as $hId => $line) {
                    $body .= "\n• [ID:{$hId}] {$line}";
                }
                if (count($foundList) > 8) {
                    $body .= "\n  ... va " . (count($foundList) - 8) . " ta boshqa";
                }
            }

            if (count($newList)) {
                $body .= "\n\n— Yangi yaratiladi (tizimda oldindan bo'lsa HEMIS ID ni belgilang) —";
                foreach (array_slice($newList, 0, 10, true) as $hId => $empName) {
                    $body .= "\n• [HEMIS ID: {$hId}] {$empName}";
                }
                if (count($newList) > 10) {
                    $body .= "\n  ... va " . (count($newList) - 10) . " ta boshqa";
                }
            }

            Notification::make()
                ->title('Tekshiruv natijasi (hech narsa yozilmadi)')
                ->body($body)
                ->info()
                ->persistent()
                ->send();
            return;
        }

        $categoryCache = [];

        // Haqiqiy sync
        $created = 0;
        $updated = 0;

        foreach ($employees as $emp) {
            $category = $this->resolveCategory($emp, $page, $categoryCache);
            $result   = $this->upsertEmployee($emp, $page, $category);

            if ($result === 'created') {
                $created++;
            } else {
                $updated++;
            }
        }

        Notification::make()
            ->title('HEMIS sync muvaffaqiyatli yakunlandi')
            ->body("Yaratildi: {$created} ta | Yangilandi: {$updated} ta")
            ->success()
            ->send();
    }

    /**
     * Xodimni topib yoki yaratib, ma'lumotlarini yangilaydi.
     * Email/ism/parol/rasm HECH QACHON o'zgartirilmaydi (faqat yangi userlarda yoziladi).
     *
     * @return string 'created' | 'updated'
     */
    private function upsertEmployee(array $emp, $page, ?StaffCategory $category): string
    {
        $employeeId    = (string) $emp['id'];
        $email         = $emp['email'] ?? null ?: null;
        $positionOrder = self::POSITION_ORDER[$emp['staffPosition']['name'] ?? ''] ?? 99;

        // HEMIS dan keladigan lavozim/bo'lim ma'lumotlari
        $hemisFields = [
            'hemis_employee_id'  => $employeeId,   // employee-list ID
            'hemis_uuid'         => $emp['uuid'] ?? null,
            'hemis_type'         => 'employee',
            'position_uz'        => $emp['staffPosition']['name'] ?? null,
            'academic_degree'    => $emp['academicDegree']['name'] ?? null,
            'academic_rank'      => $emp['academicRank']['name'] ?? null,
            'employment_form'    => $emp['employmentForm']['name'] ?? null,
            'position_order'     => $positionOrder,
            'department_page_id' => $page->id,
            'staff_category_id'  => $category?->id,
        ];

        // 1. hemis_employee_id bo'yicha topish (asosiy)
        $user = User::where('hemis_employee_id', $employeeId)->first();

        // 2. Email bo'yicha topish (oldindan qo'lda yaratilgan)
        if (! $user && $email) {
            $user = User::where('email', $email)->first();
        }

        if ($user) {
            // Mavjud user — faqat HEMIS lavozim/bo'lim ma'lumotlarini yangilash
            // ism, email, parol, rasm, rol O'ZGARTIRILMAYDI
            foreach ($hemisFields as $field => $value) {
                $user->{$field} = $value;
            }
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
            }
            $user->save();
            return 'updated';
        }

        // 3. Yangi user yaratish
        $photoPath = $this->downloadPhoto($emp['image'] ?? null, $employeeId);

        User::create(array_merge($hemisFields, [
            'name'               => $emp['full_name'] ?? 'HEMIS Xodim',
            'email'              => $email,
            'password'           => null,
            'profile_photo_path' => $photoPath,
            'email_verified_at'  => now(),
        ]));

        return 'created';
    }

    private function resolveCategory(array $emp, $page, array &$cache): ?StaffCategory
    {
        $positionName = $emp['staffPosition']['name'] ?? null;

        if (! $positionName) {
            return null;
        }

        $cacheKey = $page->id . ':' . $positionName;

        if (! array_key_exists($cacheKey, $cache)) {
            $order = self::POSITION_ORDER[$positionName] ?? 99;

            $cache[$cacheKey] = StaffCategory::firstOrCreate(
                [
                    'page_id'  => $page->id,
                    'title_uz' => $positionName,
                ],
                [
                    'title_ru' => $positionName,
                    'title_en' => $positionName,
                    'order'    => $order,
                ]
            );
        }

        return $cache[$cacheKey];
    }

    private function downloadPhoto(?string $url, string $hemisId): ?string
    {
        if (! $url) {
            return null;
        }

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
                'error'    => $e->getMessage(),
            ]);
            return null;
        }
    }
}
