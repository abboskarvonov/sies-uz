<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Activity loglarni tozalash - har kuni soat 01:00 da (7 kundan eski loglar o'chiriladi)
Schedule::command('activitylog:clean')
    ->dailyAt('01:00')
    ->timezone('Asia/Tashkent');

// Sitemap generation - har kuni soat 03:00 da (to'liq rebuild)
// Kontent o'zgarganda esa observer orqali avtomatik (2 daqiqa kechikish bilan) ishga tushadi
Schedule::command('sitemap:generate')
    ->dailyAt('03:00')
    ->timezone('Asia/Tashkent')
    ->withoutOverlapping()
    ->emailOutputOnFailure(config('mail.from.address'))
    ->appendOutputTo(storage_path('logs/sitemap-generation.log'));