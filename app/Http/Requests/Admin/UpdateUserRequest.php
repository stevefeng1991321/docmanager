<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'username'         => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9_-]+$/', 'unique:users,username,' . $userId],
            'name'             => ['required', 'string', 'max:255'],
            'role'             => ['required', 'in:admin,editor,viewer'],
            'status'           => ['required', 'in:active,inactive'],
            'storage_quota_mb' => ['nullable', 'integer', 'min:0', 'max:102400'],
        ];
    }
}
