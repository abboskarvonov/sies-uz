<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\StaffCategoryResource;
use App\Http\Resources\Api\V1\StaffMemberDetailResource;
use App\Http\Traits\Api\ApiResponses;
use App\Models\Page;
use App\Models\StaffMember;

class StaffController extends Controller
{
    use ApiResponses;

    public function show(int $id)
    {
        $staff = StaffMember::with('staffCategory')->find($id);

        if (!$staff) {
            return $this->notFoundResponse('Staff member not found');
        }

        return $this->successResponse(new StaffMemberDetailResource($staff));
    }

    public function byPage(int $id)
    {
        $page = Page::find($id);

        if (!$page) {
            return $this->notFoundResponse('Page not found');
        }

        $categories = $page->staffCategories()
            ->whereNull('parent_id')
            ->with([
                'staffMembers:id,name_uz,name_ru,name_en,position_uz,position_ru,position_en,image,staff_category_id,page_id',
                'children' => fn ($q) => $q->with('staffMembers:id,name_uz,name_ru,name_en,position_uz,position_ru,position_en,image,staff_category_id,page_id'),
            ])
            ->get();

        return $this->successResponse(StaffCategoryResource::collection($categories));
    }
}
