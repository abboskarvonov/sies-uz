<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update_page');
    }

    public function rules(): array
    {
        return [
            'title_uz' => ['sometimes', 'string', 'max:500'],
            'title_ru' => ['nullable', 'string', 'max:500'],
            'title_en' => ['nullable', 'string', 'max:500'],
            'content_uz' => ['nullable', 'string'],
            'content_ru' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'slug_uz' => ['nullable', 'string', 'max:500'],
            'slug_ru' => ['nullable', 'string', 'max:500'],
            'slug_en' => ['nullable', 'string', 'max:500'],
            'menu_id' => ['sometimes', 'exists:menus,id'],
            'submenu_id' => ['sometimes', 'exists:submenus,id'],
            'multimenu_id' => ['sometimes', 'exists:multimenus,id'],
            'page_type' => ['sometimes', 'string', 'in:blog,faculty,department,center,section,default'],
            'status' => ['sometimes', 'boolean'],
            'date' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'max:2048'],
            'activity' => ['sometimes', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }
}
