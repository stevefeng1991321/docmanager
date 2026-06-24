<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'name'     => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'in:admin,editor,viewer'],
        ];
    }
}
