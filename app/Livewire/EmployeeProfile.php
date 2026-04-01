<?php

namespace App\Livewire;

use App\Services\HemisPositionSyncService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class EmployeeProfile extends Component
{
    use WithFileUploads;

    public string $content_uz = '';
    public string $content_ru = '';
    public string $content_en = '';
    #[Validate(['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'])]
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
            'photo'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'],
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
     * HEMIS dan foydalanuvchining barcha lavozimlarini yangilash.
     * Bio, rasm, parol o'zgarmaydi — faqat HEMIS maydonlari sync bo'ladi.
     */
    public function syncFromHemis(): void
    {
        $user = Auth::user();

        if (! $user->hemis_uuid && ! $user->hemis_id && ! $user->hemis_employee_id) {
            $this->dispatch('hemis-sync-error', message: 'HEMIS ma\'lumotlari biriktirilmagan.');
            return;
        }

        $this->syncing = true;

        $synced = app(HemisPositionSyncService::class)->sync($user);

        $this->syncing = false;

        if ($synced) {
            $this->dispatch('hemis-synced');
        } else {
            $this->dispatch('hemis-sync-error', message: 'HEMIS dan ma\'lumot olinmadi. Keyinroq urinib ko\'ring.');
        }
    }

    public function render()
    {
        return view('livewire.employee-profile', [
            'user' => Auth::user()->load([
                'departmentPage',
                'pagePositions' => fn($q) => $q->orderByDesc('is_primary')->orderBy('position_order'),
                'pagePositions.page:id,title_uz,title_ru,title_en',
            ]),
        ]);
    }
}
