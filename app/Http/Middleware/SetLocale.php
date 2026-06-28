<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const SUPPORTED = ['en', 'zh'];

    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale($this->resolve($request));
        return $next($request);
    }

    private function resolve(Request $request): string
    {
        // 1. Authenticated user's saved preference
        if ($user = $request->user()) {
            if (in_array($user->locale, self::SUPPORTED, true)) {
                return $user->locale;
            }
        }

        // 2. Session (set by locale switcher before user saves preference)
        $session = $request->session()->get('locale');
        if (in_array($session, self::SUPPORTED, true)) {
            return $session;
        }

        // 3. App default
        return config('app.locale', 'en');
    }
}
