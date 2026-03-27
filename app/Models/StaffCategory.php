<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffCategory extends Model
{
    protected $fillable = [
        'page_id',
        'parent_id',
        'title_uz',
        'title_ru',
        'title_en',
        'hemis_employee_type_code',
        'order',
    ];

    public function parent()
    {
        return $this->belongsTo(StaffCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(StaffCategory::class, 'parent_id');
    }

    /**
     * Bu kategoriyaga biriktirilgan xodimlar.
     *
     * user_page_positions.staff_category_id orqali many-to-many.
     * Pivot dan position_uz/ru/en olinadi — har bir bo'lim kontekstida
     * to'g'ri lavozim ko'rsatiladi (masalan, dekan vs o'qituvchi).
     */
    public function employees()
    {
        return $this->belongsToMany(
            User::class,
            'user_page_positions',
            'staff_category_id',
            'user_id'
        )
        ->withPivot(['position_uz', 'position_ru', 'position_en', 'position_order', 'employment_form', 'page_id'])
        ->orderByPivot('position_order')
        ->orderBy('users.name');
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
