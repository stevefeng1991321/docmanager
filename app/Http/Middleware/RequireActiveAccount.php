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

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->status === 'pending') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->with('status', 'pending')
                ->with('message', 'Your account is awaiting activation by an administrator.');
        }

        if ($user->status === 'inactive') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->with('status', 'inactive')
                ->with('message', 'Your account has been deactivated. Please contact support.');
        }

        return $next($request);
    }
}
