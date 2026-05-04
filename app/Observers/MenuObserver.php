<?php

namespace App\Observers;

use App\Jobs\RegenerateSitemap;

class MenuObserver
{
    public function saved(): void
    {
        RegenerateSitemap::dispatch()->delay(now()->addMinutes(2));
    }

    public function deleted(): void
    {
        RegenerateSitemap::dispatch()->delay(now()->addMinutes(2));
    }
}
