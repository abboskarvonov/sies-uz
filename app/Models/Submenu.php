<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Submenu extends Model implements Sortable
{
    use HasFactory, SortableTrait, LogsActivity;

    protected $fillable = [
        'old_id',
        'title_uz',
        'title_ru',
        'title_en',
        'slug_uz',
        'slug_ru',
        'slug_en',
        'link',
        'type',
        'menu_id',
        'position',
        'order',
        'image',
        'status',
        'created_by',
        'updated_by'
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

        static::deleting(function ($submenu) {
            if ($submenu->image && Storage::disk('public')->exists($submenu->image)) {
                Storage::disk('public')->delete($submenu->image);
            }
        });

        static::saved(fn () => Menu::clearApiMenuCache());
        static::deleted(fn () => Menu::clearApiMenuCache());
    }

    public function getSlug($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $column = 'slug_' . $locale;
        return $this->$column;
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function multimenus()
    {
        return $this->hasMany(Multimenu::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // barcha maydonlarni log qiladi
            ->useLogName('page'); // log nomi
    }
}
