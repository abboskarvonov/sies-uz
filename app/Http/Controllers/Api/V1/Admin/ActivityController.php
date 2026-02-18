<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\ActivityResource;
use App\Http\Traits\Api\ApiResponses;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Activity::class);

        $query = Activity::with('causer:id,name');

        if ($request->filled('log_name')) {
            $query->where('log_name', $request->input('log_name'));
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->input('subject_type'));
        }

        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->integer('causer_id'));
        }

        $perPage = min($request->integer('per_page', 15), 50);

        return $this->paginatedResponse(
            ActivityResource::collection($query->latest()->paginate($perPage))
        );
    }
}
