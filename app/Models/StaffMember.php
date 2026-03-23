<?php

namespace App\Models;

use Throwable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StaffMember extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id',
        'name_uz',
        'name_ru',
        'name_en',
        'position_uz',
        'position_ru',
        'position_en',
        'content_uz',
        'content_ru',
        'content_en',
        'page_id',
        'image',
        'staff_category_id',
        'created_by',
        'updated_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function staffCategory()
    {
        return $this->belongsTo(StaffCategory::class, 'staff_category_id');
    }

    public function pages()
    {
        return $this->belongsToMany(Page::class, 'page_staff_member');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

        static::deleting(function ($file) {
            if ($file->file && Storage::disk('public')->exists($file->file)) {
                Storage::disk('public')->delete($file->file);
            }
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // barcha maydonlarni log qiladi
            ->useLogName('page'); // log nomi
    }

    /**
     * Image accessor - fayl yo'lini validate qilish
     */
    public function getImageAttribute($value): ?string
    {
        if (!$value || empty(trim($value))) {
            return null;
        }

        $value = trim($value);

        // Faqat "staff_members/" bo'lsa - invalid
        if ($value === 'staff_members/' || $value === 'staff_members') {
            return null;
        }

        // Agar "staff_members/" bilan boshlansa - tekshirish
        if (str_starts_with($value, 'staff_members/')) {
            if (Storage::disk('public')->exists($value)) {
                return $value;
            }
            return null; // Fayl yo'q
        }

        // Faqat fayl nomi bo'lsa - prefix qo'shish
        if (!str_contains($value, '/')) {
            $fullPath = 'staff_members/' . $value;
            if (Storage::disk('public')->exists($fullPath)) {
                return $fullPath;
            }
            return null;
        }

        // Qo'shimcha tekshirish
        if (Storage::disk('public')->exists($value)) {
            return $value;
        }

        return null;
    }

    /**
     * Image mutator
     */
    public function setImageAttribute($value): void
    {
        // Filament FileUpload array qaytarishi mumkin — birinchi elementni olamiz
        if (is_array($value)) {
            $value = $value[0] ?? null;
        }

        // String bo'lsa sanitize qilish
        if ($value && is_string($value)) {
            $value = trim($value);

            // Prefix yo'q bo'lsa qo'shish
            if (!str_starts_with($value, 'staff_members/') && !empty($value)) {
                $value = 'staff_members/' . $value;
            }
        } else {
            $value = null;
        }

        // Eski faylni o'chirish
        if ($this->exists && $this->getOriginal('image')) {
            $oldImage = trim($this->getOriginal('image'));

            // Invalid bo'lsa o'chirmaslik
            if ($oldImage !== 'staff_members/' && !empty($oldImage) && $oldImage !== $value) {
                if (Storage::disk('public')->exists($oldImage)) {
                    try {
                        Storage::disk('public')->delete($oldImage);
                    } catch (Throwable $e) {
                        Log::error('Failed to delete staff image', ['path' => $oldImage]);
                    }
                }
            }
        }

        $this->attributes['image'] = $value;
    }
}
