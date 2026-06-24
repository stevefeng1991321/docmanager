<?php

namespace App\Providers;

use App\Support\PasswordPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // All Password::defaults() calls across the app pick up the admin-configured complexity level.
        Password::defaults(fn () => PasswordPolicy::rule());

        RateLimiter::for('api-login', function (Request $request) {
            // 5 attempts per minute per IP — tight, but login shouldn't be called frequently
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('api-download', function (Request $request) {
            // 30 downloads per minute per authenticated user (or IP as fallback)
            return Limit::perMinute(30)->by($request->user()?->id ?? $request->ip());
        });
    }
}
