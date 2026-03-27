<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Xodimning bitta bo'lim/kafedradagi lavozimi.
 *
 * Bitta xodim bir nechta bo'limda ishlay oladi — har biri uchun alohida yozuv.
 * is_primary = true — asosiy lavozim (users.department_page_id bilan mos keladi).
 */
class UserPagePosition extends Model
{
    protected $fillable = [
        'user_id',
        'page_id',
        'staff_category_id',
        'position_uz',
        'position_ru',
        'position_en',
        'position_order',
        'employment_form',
        'hemis_employee_type_code',
        'hemis_position_id',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary'     => 'boolean',
            'position_order' => 'integer',
        ];
    }

    // ─── Relations ────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function staffCategory(): BelongsTo
    {
        return $this->belongsTo(StaffCategory::class);
    }
}
