<?php

namespace App\Livewire;

use App\Services\HemisApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EmployeeProfile extends Component
{
    use WithFileUploads;

    public string $content_uz = '';
    public string $content_ru = '';
    public string $content_en = '';
    public $photo;

    public bool $syncing = false;

    public function mount(): void
    {
        $user = Auth::user();
        $this->content_uz = $user->content_uz ?? '';
        $this->content_ru = $user->content_ru ?? '';
        $this->content_en = $user->content_en ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'content_uz' => 'nullable|string|max:10000',
            'content_ru' => 'nullable|string|max:10000',
            'content_en' => 'nullable|string|max:10000',
            'photo'      => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();

        $user->content_uz = $this->content_uz ?: null;
        $user->content_ru = $this->content_ru ?: null;
        $user->content_en = $this->content_en ?: null;

        if ($this->photo) {
            if ($user->profile_photo_path && str_starts_with($user->profile_photo_path, 'profile-photos/custom-')) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $path = $this->photo->storeAs(
                'profile-photos',
                'custom-' . $user->id . '.' . $this->photo->getClientOriginalExtension(),
                'public'
            );
            $user->profile_photo_path = $path;
            $this->photo = null;
        }

        $user->save();

        $this->dispatch('profile-saved');
    }

    /**
     * HEMIS dan foydalanuvchining o'z ma'lumotlarini yangilash.
     * Faqat HEMIS maydonlari yangilanadi — bio, rasm, parol o'zgarmaydi.
     */
    public function syncFromHemis(): void
    {
        $user = Auth::user();

        if (empty($user->hemis_employee_id)) {
            $this->dispatch('hemis-sync-error', message: 'HEMIS Employee ID biriktirilmagan.');
            return;
        }

        $this->syncing = true;

        $api = app(HemisApiService::class);
        $emp = $api->fetchEmployee($user->hemis_employee_id);

        if (! $emp) {
            $this->syncing = false;
            $this->dispatch('hemis-sync-error', message: 'HEMIS dan ma\'lumot olinmadi.');
            return;
        }

        $positionOrders = [
            'Rektor' => 1, "O'quv ishlari bo'yicha birinchi prorektor" => 2,
            "Yoshlar bilan ishlash bo'yicha prorektor" => 3, 'Dekan' => 6,
            'Kafedra mudiri' => 9, 'Professor' => 10, 'Dotsent' => 11,
            "Katta o'qituvchi" => 12, 'Assistent' => 13,
        ];

        $user->name           = $emp['full_name'] ?? $user->name;
        $user->position_uz    = $emp['staffPosition']['name'] ?? $user->position_uz;
        $user->academic_degree = $emp['academicDegree']['name'] ?? $user->academic_degree;
        $user->academic_rank  = $emp['academicRank']['name'] ?? $user->academic_rank;
        $user->employment_form = $emp['employmentForm']['name'] ?? $user->employment_form;
        $user->position_order = $positionOrders[$emp['staffPosition']['name'] ?? ''] ?? $user->position_order ?? 99;
        $user->save();

        $this->syncing = false;
        $this->dispatch('hemis-synced');
    }

    public function render()
    {
        return view('livewire.employee-profile', [
            'user' => Auth::user()->load('departmentPage'),
        ]);
    }
}
