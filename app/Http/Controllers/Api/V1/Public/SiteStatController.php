<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SiteStatResource;
use App\Http\Traits\Api\ApiResponses;
use App\Models\SiteStat;
use Illuminate\Support\Facades\Cache;

class SiteStatController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $ttl = config('site.cache.ttl.stats', 3600);

        $stats = config('site.cache.enabled')
            ? Cache::remember('api:stats', $ttl, fn () => SiteStat::first())
            : SiteStat::first();

        if (!$stats) {
            return $this->notFoundResponse('Statistics not found');
        }

        return $this->successResponse(new SiteStatResource($stats));
    }
}
