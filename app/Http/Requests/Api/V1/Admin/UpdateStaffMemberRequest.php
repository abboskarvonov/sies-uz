<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        $staffMember = $this->route('staff');

        if ($this->user()->can('update_staff::member')) {
            return true;
        }

        if ($this->user()->can('view_all_pages')) {
            return true;
        }

        if ($staffMember && $staffMember->page_id) {
            return $this->user()->assignedPages()
                ->where('pages.id', $staffMember->page_id)
                ->exists();
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'name_uz' => ['sometimes', 'string', 'max:255'],
            'name_ru' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'position_uz' => ['nullable', 'string', 'max:255'],
            'position_ru' => ['nullable', 'string', 'max:255'],
            'position_en' => ['nullable', 'string', 'max:255'],
            'content_uz' => ['nullable', 'string'],
            'content_ru' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'page_id' => ['sometimes', 'exists:pages,id'],
            'staff_category_id' => ['nullable', 'exists:staff_categories,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
