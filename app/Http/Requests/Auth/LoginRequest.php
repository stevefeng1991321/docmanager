<?php

namespace App\Http\Requests\Auth;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $maxAttempts = (int) config('auth.lockout_attempts', 5);
        $lockoutMinutes = (int) config('auth.lockout_minutes', 15);

        // Check if user account is already locked
        $user = User::where('username', $this->string('username'))->first();
        if ($user && $user->isLocked()) {
            $remaining = now()->diffInMinutes($user->locked_until, false);
            throw ValidationException::withMessages([
                'username' => "Account is locked. Try again in {$remaining} minute(s).",
            ]);
        }

        if (! Auth::attempt(['username' => $this->string('username'), 'password' => $this->string('password')], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            // Track failed attempts on the user record
            if ($user) {
                $attempts = $user->failed_login_attempts + 1;
                $update = ['failed_login_attempts' => $attempts];
                if ($attempts >= $maxAttempts) {
                    $update['locked_until'] = now()->addMinutes($lockoutMinutes);
                }
                $user->update($update);

                ActivityLog::create([
                    'user_id'    => $user->id,
                    'event'      => 'login.failed',
                    'ip_address' => $this->ip(),
                    'user_agent' => $this->userAgent(),
                    'details'    => ['attempts' => $attempts],
                ]);
            }

            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

        // Successful login — reset failed attempts
        Auth::user()->update([
            'failed_login_attempts' => 0,
            'locked_until'          => null,
            'last_login_at'         => now(),
        ]);

        ActivityLog::create([
            'user_id'    => Auth::id(),
            'event'      => 'login.success',
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 60)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::lower($this->string('username')).'|'.$this->ip();
    }
}
