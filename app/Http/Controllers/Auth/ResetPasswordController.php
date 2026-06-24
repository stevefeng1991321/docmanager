<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AccountRequest;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    public function create(string $token)
    {
        $accountRequest = $this->findValidRequest($token);

        if (!$accountRequest) {
            return redirect()->route('login')
                ->with('message', 'This password reset link is invalid or has expired.');
        }

        return view('auth.reset-password', compact('token'));
    }

    public function store(Request $request, string $token)
    {
        $accountRequest = $this->findValidRequest($token);

        if (!$accountRequest) {
            return redirect()->route('login')
                ->with('message', 'This password reset link is invalid or has expired.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $accountRequest->user;

        if (!$user) {
            return redirect()->route('login')
                ->with('message', 'User account no longer exists.');
        }

        $user->update(['password' => Hash::make($request->password)]);

        $accountRequest->update([
            'status'                 => 'approved',
            'reset_token'            => null,
            'reset_token_expires_at' => null,
        ]);

        AuditLog::record('user.password_reset_via_link', null, ['user_id' => $user->id]);

        return redirect()->route('login')
            ->with('message', 'Your password has been reset. You may now sign in.');
    }

    private function findValidRequest(string $token): ?AccountRequest
    {
        return AccountRequest::where('reset_token', $token)
            ->where('type', 'password_reset')
            ->where('status', 'pending')
            ->where('reset_token_expires_at', '>', now())
            ->with('user')
            ->first();
    }
}
