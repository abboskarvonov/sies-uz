<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\SlugHelper;
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

class Multimenu extends Model implements Sortable, HasMedia
{
    use HasFactory, SortableTrait, LogsActivity, InteractsWithMedia;

    protected $fillable = [
        'old_id',
        'menu_id',
        'submenu_id',
        'title_uz',
        'title_ru',
        'title_en',
        'slug_uz',
        'slug_ru',
        'slug_en',
        'link',
        'status',
        'order',
        'image',
        'created_by',
        'updated_by',
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

            if (empty($model->slug_uz)) {
                $model->slug_uz = SlugHelper::generateUniqueSlug(static::class, 'slug_uz', $model->title_uz);
            }
            if (empty($model->slug_ru)) {
                $model->slug_ru = SlugHelper::generateUniqueSlug(static::class, 'slug_ru', $model->title_ru);
            }
            if (empty($model->slug_en)) {
                $model->slug_en = SlugHelper::generateUniqueSlug(static::class, 'slug_en', $model->title_en);
            }
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

        static::deleting(function ($multimenu) {
            $legacy = $multimenu->attributes['image'] ?? null;
            if ($legacy && Storage::disk('public')->exists($legacy)) {
                Storage::disk('public')->delete($legacy);
            }
        });

        static::saved(fn () => Menu::clearApiMenuCache());
        static::deleted(fn () => Menu::clearApiMenuCache());
    }

    public function getSlug($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->{'slug_' . $locale};
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function submenu()
    {
        return $this->belongsTo(Submenu::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function pages()
    {
        return $this->belongsToMany(Page::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('page');
    }
}
