<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreStaffMemberRequest;
use App\Http\Requests\Api\V1\Admin\UpdateStaffMemberRequest;
use App\Http\Resources\Api\V1\Admin\StaffMemberResource;
use App\Http\Traits\Api\ApiResponses;
use App\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StaffMemberController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', StaffMember::class);

        $query = StaffMember::with(['createdBy:id,name', 'updatedBy:id,name']);

        if ($request->filled('page_id')) {
            $query->where('page_id', $request->integer('page_id'));
        }

        if ($request->filled('staff_category_id')) {
            $query->where('staff_category_id', $request->integer('staff_category_id'));
        }

        $perPage = min($request->integer('per_page', 15), 50);

        return $this->paginatedResponse(
            StaffMemberResource::collection($query->latest()->paginate($perPage))
        );
    }

    public function show(int $staff)
    {
        $staff = StaffMember::with(['createdBy:id,name', 'updatedBy:id,name'])->find($staff);

        if (!$staff) {
            return $this->notFoundResponse('Staff member not found');
        }

        Gate::authorize('view', $staff);

        return $this->successResponse(new StaffMemberResource($staff));
    }

    public function store(StoreStaffMemberRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('staff_members', 'public');
        }

        $staff = StaffMember::create($data);
        $staff->load(['createdBy:id,name', 'updatedBy:id,name']);

        return $this->successResponse(new StaffMemberResource($staff), 201);
    }

    public function update(UpdateStaffMemberRequest $request, int $staff)
    {
        $staff = StaffMember::find($staff);

        if (!$staff) {
            return $this->notFoundResponse('Staff member not found');
        }

        Gate::authorize('update', $staff);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('staff_members', 'public');
        }

        $staff->update($data);
        $staff->load(['createdBy:id,name', 'updatedBy:id,name']);

        return $this->successResponse(new StaffMemberResource($staff));
    }

    public function destroy(int $staff)
    {
        $staff = StaffMember::find($staff);

        if (!$staff) {
            return $this->notFoundResponse('Staff member not found');
        }

        Gate::authorize('delete', $staff);

        $staff->delete();

        return $this->successResponse(['message' => 'Staff member deleted successfully.']);
    }
}
