<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireActiveAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        $isApi = $request->expectsJson() || $request->is('api/*');

        if (!$user) {
            return $isApi
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('login');
        }

        if ($user->status === 'pending') {
            Auth::logout();
            if (!$isApi) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return $isApi
                ? response()->json(['message' => 'Account is pending activation.'], 403)
                : redirect()->route('login')
                    ->with('status', 'pending')
                    ->with('message', 'Your account is awaiting activation by an administrator.');
        }

        if ($user->status === 'inactive') {
            Auth::logout();
            if (!$isApi) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return $isApi
                ? response()->json(['message' => 'Account is deactivated.'], 403)
                : redirect()->route('login')
                    ->with('status', 'inactive')
                    ->with('message', 'Your account has been deactivated. Please contact support.');
        }

        if (!$user->last_seen_at || $user->last_seen_at->lt(now()->subMinute())) {
            $user->forceFill(['last_seen_at' => now()])->saveQuietly();
        }

        return $next($request);
    }
}
