<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MergeTagsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_id' => ['required', 'exists:tags,id'],
            'target_id' => ['required', 'exists:tags,id', 'different:source_id'],
        ];
    }
}
