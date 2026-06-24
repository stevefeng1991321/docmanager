<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

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
            'password' => ['required', Password::defaults()],
            'role'     => ['required', 'in:admin,editor,viewer'],
        ];
    }
}
