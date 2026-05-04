<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Symbol extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title_uz',
        'title_ru',
        'title_en',
        'slug_uz',
        'slug_ru',
        'slug_en',
        'content_uz',
        'content_ru',
        'content_en',
        'image',
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
}
