<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SiteSettings extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'site_name_uz',
        'site_name_ru',
        'site_name_en',
        'address_uz',
        'address_ru',
        'address_en',
        'phone_primary',
        'phone_secondary',
        'email_primary',
        'email_secondary',
        'telegram_url',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'hemis_url',
        'arm_url',
        'sdg_url',
        'logo',
        'map_embed_url',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
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
            ->width(200)
            ->format('webp')
            ->quality(80)
            ->nonQueued();
    }

    public static function instance(): static
    {
        return static::firstOrCreate([]);
    }

    public function siteName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        return $this->{"site_name_{$locale}"}
            ?? $this->site_name_uz
            ?? config('app.name', 'SIES');
    }

    public function address(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        return $this->{"address_{$locale}"} ?? $this->address_uz;
    }

    public function logoUrl(): string
    {
        $url = $this->getFirstMediaUrl('logo', 'webp');
        if ($url) return $url;

        // Legacy fallback
        $legacy = $this->attributes['logo'] ?? null;
        if ($legacy) return asset('storage/' . $legacy);

        return asset('img/logo.webp');
    }
}
