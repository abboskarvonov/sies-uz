<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create_staff::member')
            || $this->user()->can('view_all_pages')
            || $this->user()->assignedPages()
                ->whereIn('pages.page_type', ['department', 'faculty', 'center', 'section'])
                ->exists();
    }

    public function rules(): array
    {
        return [
            'name_uz' => ['required', 'string', 'max:255'],
            'name_ru' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'position_uz' => ['nullable', 'string', 'max:255'],
            'position_ru' => ['nullable', 'string', 'max:255'],
            'position_en' => ['nullable', 'string', 'max:255'],
            'content_uz' => ['nullable', 'string'],
            'content_ru' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'page_id' => ['required', 'exists:pages,id'],
            'staff_category_id' => ['nullable', 'exists:staff_categories,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
