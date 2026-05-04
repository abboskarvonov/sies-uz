<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RegenerateSitemap implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    // Queue da faqat bitta nusxa bo'ladi (debounce effekti)
    public int $uniqueFor = 120; // 2 daqiqa ichida dispatch qilingan takroriy joblar e'tiborga olinmaydi

    public function handle(): void
    {
        try {
            Artisan::call('sitemap:generate');
            Log::channel('daily')->info('Sitemap successfully regenerated via job.');
        } catch (\Throwable $e) {
            Log::channel('daily')->error('Sitemap regeneration failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
