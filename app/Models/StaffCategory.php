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

    public function staffMembers()
    {
        return $this->hasMany(StaffMember::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}