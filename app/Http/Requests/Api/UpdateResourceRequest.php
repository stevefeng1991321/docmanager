<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['exists:tags,id'],
            'status'      => ['nullable', 'in:draft,pending_review,published,rejected'],
        ];
    }
}
