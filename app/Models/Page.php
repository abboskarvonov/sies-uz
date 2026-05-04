<?php

namespace App\Models;

use Exception;
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

class Page extends Model implements Sortable, HasMedia
{
    use HasFactory, SortableTrait, LogsActivity, InteractsWithMedia;

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
        'hemis_id',
        'parent_page_id',
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('gallery')
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

    public function galleryUrls(string $conversion = 'webp'): array
    {
        $media = $this->getMedia('gallery');
        if ($media->isNotEmpty()) {
            return $media->map(fn($m) => $m->getUrl($conversion))->toArray();
        }

        $raw = $this->attributes['images'] ?? null;
        if ($raw) {
            $paths = is_array($raw) ? $raw : json_decode($raw, true);
            if (!is_array($paths)) {
                $paths = $paths ? [$paths] : [];
            }
            return array_map(fn($p) => asset('storage/' . $p), array_filter($paths));
        }
        return [];
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

        // Legacy files cleanup (medialibrary handles its own files automatically)
        static::deleting(function ($page) {
            $legacy = $page->attributes['image'] ?? null;
            if ($legacy && Storage::disk('public')->exists($legacy)) {
                Storage::disk('public')->delete($legacy);
            }
            $raw = $page->attributes['images'] ?? null;
            if ($raw) {
                $paths = is_array($raw) ? $raw : json_decode($raw, true);
                foreach (array_filter($paths ?? []) as $path) {
                    try {
                        if (Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->delete($path);
                        }
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
        });

        static::saved(fn () => self::clearHomepageCache());
        static::deleted(fn () => self::clearHomepageCache());
    }

    public function parentPage()
    {
        return $this->belongsTo(Page::class, 'parent_page_id');
    }

    public function childPages()
    {
        return $this->hasMany(Page::class, 'parent_page_id');
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
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
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

    public function staffCategories()
    {
        return $this->hasMany(StaffCategory::class)->orderBy('order');
    }

    public function employees()
    {
        return $this->hasMany(User::class, 'department_page_id')
                    ->orderBy('position_order')
                    ->orderBy('name');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('page_type', $type);
    }

    public function scopeInMenu($query, $menuId, $submenuId = null, $multimenuId = null)
    {
        return $query->when($menuId, fn($q) => $q->where('menu_id', $menuId))
            ->when($submenuId, fn($q) => $q->where('submenu_id', $submenuId))
            ->when($multimenuId, fn($q) => $q->where('multimenu_id', $multimenuId));
    }

    public function scopeLatestByDate($query)
    {
        return $query->latest('date');
    }

    public function departmentHistory()
    {
        return $this->hasOne(DepartmentHistory::class, 'department_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('page');
    }

    public static function clearHomepageCache(): void
    {
        foreach (['uz', 'ru', 'en'] as $locale) {
            Cache::forget("homepage_data_{$locale}");
        }
    }
}
