<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
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

    /**
     * Tizimda bitta yozuv bo'ladi — singleton olish.
     */
    public static function instance(): static
    {
        return static::firstOrCreate([]);
    }

    /**
     * Tilga mos site_name qaytaradi (uz fallback).
     */
    public function siteName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        return $this->{"site_name_{$locale}"}
            ?? $this->site_name_uz
            ?? config('app.name', 'SIES');
    }

    /**
     * Tilga mos address qaytaradi (uz fallback).
     */
    public function address(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        return $this->{"address_{$locale}"} ?? $this->address_uz;
    }

    /**
     * Logo URL (fallback: /img/logo.webp).
     */
    public function logoUrl(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return asset('img/logo.webp');
    }
}
