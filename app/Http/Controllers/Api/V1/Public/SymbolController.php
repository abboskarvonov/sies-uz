<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SymbolResource;
use App\Http\Traits\Api\ApiResponses;
use App\Models\Symbol;

class SymbolController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $symbols = Symbol::all();

        return $this->successResponse(SymbolResource::collection($symbols));
    }

    public function show(string $slug)
    {
        $locale = app()->getLocale();

        $symbol = Symbol::where("slug_{$locale}", $slug)
            ->orWhere('slug_uz', $slug)
            ->first();

        if (!$symbol) {
            return $this->notFoundResponse('Symbol not found');
        }

        return $this->successResponse(new SymbolResource($symbol));
    }
}
