<?php

namespace App\Observers;

use App\Jobs\RegenerateSitemap;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class PageObserver
{
    public function saved(Page $page): void
    {
        $this->clearHomepageCache();
        $this->scheduleSitemapRegen();
    }

    public function deleted(Page $page): void
    {
        $this->clearHomepageCache();
        $this->scheduleSitemapRegen();
    }

    private function clearHomepageCache(): void
    {
        foreach (['uz', 'ru', 'en'] as $locale) {
            Cache::forget("homepage_data_{$locale}");
        }
    }

    private function scheduleSitemapRegen(): void
    {
        RegenerateSitemap::dispatch()->delay(now()->addMinutes(2));
    }
}
