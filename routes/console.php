<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sitemap generation - haftada 2 marta (Dushanba va Payshanba, soat 02:00 da)
Schedule::command('sitemap:generate')
    ->weeklyOn([1, 4], '02:00') // Dushanba (1) va Payshanba (4), soat 02:00
    ->timezone('Asia/Tashkent')
    ->emailOutputOnFailure(config('mail.from.address'))
    ->appendOutputTo(storage_path('logs/sitemap-generation.log'));