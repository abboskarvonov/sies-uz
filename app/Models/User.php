<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\CompressesImages;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasApiTokens, HasRoles, HasPanelShield, LogsActivity;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use CompressesImages;

    protected array $compressibleImageFields = ['profile_photo_path'];

    // ─── Email verification override ────────────────────────────────
    // HEMIS orqali kirgan xodim va talabalar email verifikatsiyasiz kirishi mumkin:
    // email bo'lmasligi yoki tasdiqlanmaganligining ahamiyati yo'q.
    public function hasVerifiedEmail(): bool
    {
        if (in_array($this->hemis_type, ['employee', 'student'])) {
            return true;
        }

        return ! is_null($this->email_verified_at);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        // HEMIS employee/student orqali kirganlar admin panelga kira olmaydi
        if (in_array($this->hemis_type, ['employee', 'student'])) {
            return false;
        }

        return $this->can('access_filament_panel');
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'hemis_id',
        'hemis_employee_id',
        'hemis_uuid',
        'hemis_type',
        'department_page_id',
        'staff_category_id',
        'position_uz',
        'position_ru',
        'position_en',
        'academic_degree',
        'academic_rank',
        'employment_form',
        'position_order',
        'content_uz',
        'content_ru',
        'content_en',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'last_seen_at'      => 'datetime',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────

    public function departmentPage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'department_page_id');
    }

    public function staffCategory(): BelongsTo
    {
        return $this->belongsTo(StaffCategory::class);
    }

    public function assignedPages()
    {
        return $this->belongsToMany(Page::class, 'page_user');
    }

    /** Xodimning barcha lavozimlari (bir nechta bo'lim/kafedrada). */
    public function pagePositions(): HasMany
    {
        return $this->hasMany(UserPagePosition::class)
            ->orderByDesc('is_primary')
            ->orderBy('position_order');
    }

    /** Asosiy lavozimi (is_primary = true). */
    public function primaryPosition(): HasOne
    {
        return $this->hasOne(UserPagePosition::class)->where('is_primary', true);
    }

    // ─── Helpers ────────────────────────────────────────────────────

    public function isEmployee(): bool
    {
        return $this->hemis_type === 'employee';
    }

    public function isStudent(): bool
    {
        return $this->hemis_type === 'student';
    }

    public function isAdmin(): bool
    {
        return $this->hemis_type === 'admin';
    }

    public function hasAccessToPage(Page $page): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        if ($this->can('view_all_pages')) {
            return true;
        }

        if ($page->page_type === 'blog' && $this->can('view_blog_pages')) {
            return true;
        }

        // HEMIS orqali ulangan lavozim sahifalarini tekshirish
        if ($this->pagePositions()->where('page_id', $page->id)->exists()) {
            return true;
        }

        // Admin tomonidan qo'lda biriktirilgan sahifalar
        return $this->assignedPages()->where('pages.id', $page->id)->exists();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('user');
    }
}
