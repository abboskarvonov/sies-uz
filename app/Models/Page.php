<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Page extends Model implements Sortable
{
    use HasFactory, SortableTrait, LogsActivity;

    protected $fillable = [
        'old_id',
        'menu_id',
        'submenu_id',
        'multimenu_id',
        'title_uz',
        'title_ru',
        'title_en',
        'content_uz',
        'content_ru',
        'content_en',
        'slug_uz',
        'slug_ru',
        'slug_en',
        'status',
        'order',
        'image',
        'images',
        'tag',
        'page_type',
        'date',
        'activity',
        'views',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'images' => 'array',
        'date' => 'date',
        'activity' => 'boolean',
    ];

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
            $model->order = static::max('order') + 1;
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

        static::deleting(function ($page) {
            if ($page->image && Storage::disk('public')->exists($page->image)) {
                Storage::disk('public')->delete($page->image);
            }
            if ($page->images && is_array($page->images)) {
                foreach ($page->images as $image) {
                    try {
                        if (Storage::disk('public')->exists($image)) {
                            Storage::disk('public')->delete($image);
                        }
                    } catch (\Exception $e) {
                        // Agar nimadir xato bo‘lsa (masalan, noto‘g‘ri path) – shunchaki o'tkazib yuboriladi
                        continue;
                    }
                }
            }
        });
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function submenu()
    {
        return $this->belongsTo(Submenu::class);
    }

    public function multimenu()
    {
        return $this->belongsTo(Multimenu::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function multimenus()
    {
        return $this->belongsToMany(Multimenu::class, 'page_multimenu');
    }

    public function files()
    {
        return $this->hasMany(PageFile::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // barcha maydonlarni log qiladi
            ->useLogName('page'); // log nomi
    }
}
