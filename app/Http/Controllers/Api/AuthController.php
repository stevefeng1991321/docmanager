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

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
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

        if ($user->isLocked()) {
            return response()->json(['message' => 'Account is temporarily locked.'], 423);
        }

        $token = $user->createToken('api-token', ['*'], now()->addDays(30))->plainTextToken;

        ActivityLog::record('api.login', []);

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
