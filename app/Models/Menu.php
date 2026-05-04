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
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Menu extends Model implements Sortable, HasMedia
{
    use HasFactory, SortableTrait, LogsActivity, InteractsWithMedia;

    protected $fillable = [
        'old_id',
        'title_uz',
        'title_ru',
        'title_en',
        'slug_uz',
        'slug_ru',
        'slug_en',
        'link',
        'menu_type',
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(80)
            ->nonQueued();

        $this->addMediaConversion('thumb')
            ->width(400)
            ->format('webp')
            ->quality(80)
            ->nonQueued();
    }

    public function imageUrl(string $conversion = 'webp'): string
    {
        $url = $this->getFirstMediaUrl('image', $conversion);
        if ($url) return $url;

        $legacy = $this->attributes['image'] ?? null;
        return $legacy ? asset('storage/' . $legacy) : '';
    }

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

        static::deleting(function ($menu) {
            $legacy = $menu->attributes['image'] ?? null;
            if ($legacy && Storage::disk('public')->exists($legacy)) {
                Storage::disk('public')->delete($legacy);
            }
        });

        static::saved(fn () => static::clearApiMenuCache());
        static::deleted(fn () => static::clearApiMenuCache());
    }

    public static function clearApiMenuCache(): void
    {
        foreach (['uz', 'ru', 'en'] as $locale) {
            Cache::forget("api:menu_tree_{$locale}");
        }
    }

    public function getSlug($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->{'slug_' . $locale};
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function submenus()
    {
        return $this->hasMany(Submenu::class);
    }

    public function multimenus()
    {
        return $this->hasMany(Multimenu::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('page');
    }
}
