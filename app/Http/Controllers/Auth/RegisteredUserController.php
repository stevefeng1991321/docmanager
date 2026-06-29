<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'name'     => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'username.regex' => 'Username may only contain letters, numbers, underscores, and hyphens.',
        ]);

        User::create([
            'username' => $request->username,
            'name'     => $request->name,
            'password' => $request->password,   // cast to hashed automatically
            'role'     => 'viewer',
            'status'   => UserStatus::Pending,
        ]);

        AuditLog::create([
            'action'  => 'user.registered',
            'details' => ['username' => $request->username],
        ]);

        return redirect()->route('login')
            ->with('status', 'registered')
            ->with('message', 'Account created. You can log in once an administrator activates your account.');
    }
}
