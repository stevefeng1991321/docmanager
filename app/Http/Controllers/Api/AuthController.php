<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $maxAttempts    = (int) config('auth.lockout_attempts', 5);
        $lockoutMinutes = (int) config('auth.lockout_minutes', 15);

        $user = User::where('username', $request->username)->first();

        // Check lockout before touching credentials (don't reveal lock status to non-users)
        if ($user && $user->isLocked()) {
            $remaining = (int) now()->diffInMinutes($user->locked_until, false);
            return response()->json([
                'message' => "Account is temporarily locked. Try again in {$remaining} minute(s).",
            ], 423);
        }

        // Validate credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($user) {
                $attempts = $user->failed_login_attempts + 1;
                $update   = ['failed_login_attempts' => $attempts];

                if ($attempts >= $maxAttempts) {
                    $update['locked_until'] = now()->addMinutes($lockoutMinutes);
                }

                $user->update($update);

                ActivityLog::create([
                    'user_id'    => $user->id,
                    'event'      => 'api.login.failed',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'details'    => json_encode(['attempts' => $attempts]),
                    'created_at' => now(),
                ]);
            }

            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->isPending()) {
            return response()->json(['message' => 'Account is pending activation.'], 403);
        }

        if (!$user->isActive()) {
            return response()->json(['message' => 'Account is inactive.'], 403);
        }

        // Success — reset lockout counters
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until'          => null,
            'last_login_at'         => now(),
        ]);

        $token = $user->createToken('api-token', ['*'], now()->addDays(30))->plainTextToken;

        ActivityLog::create([
            'user_id'    => $user->id,
            'event'      => 'api.login.success',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return response()->json([
            'token'      => $token,
            'token_type' => 'Bearer',
            'expires_in' => 30 * 24 * 60 * 60,
            'user' => [
                'id'       => $user->id,
                'username' => $user->username,
                'name'     => $user->name,
                'role'     => $user->role,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id'         => $user->id,
            'username'   => $user->username,
            'name'       => $user->name,
            'role'       => $user->role,
            'created_at' => $user->created_at?->toIso8601String(),
        ]);
    }
}
