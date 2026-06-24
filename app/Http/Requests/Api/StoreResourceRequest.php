<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file'        => ['required', 'file', 'max:' . (config('app.max_upload_size_mb', 50) * 1024)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['exists:tags,id'],
            'status'      => ['nullable', 'in:draft,pending_review,published'],
        ];
    }
}
