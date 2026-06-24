<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center">

<div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-lg">

    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-600 text-white mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Set New Password</h1>
        <p class="text-sm text-gray-500 mt-1">Choose a strong password for your account.</p>
    </div>

    <form method="POST" action="{{ route('password.store', $token) }}" class="space-y-5">
        @csrf

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input id="password" type="password" name="password"
                   required autofocus autocomplete="new-password"
                   class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-400 @else border-gray-300 @enderror">
            <p class="mt-1 text-xs text-gray-400">{{ \App\Support\PasswordPolicy::description() }}</p>
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   required autocomplete="new-password"
                   class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 border-gray-300">
        </div>

        <button type="submit"
                class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            Reset Password
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Back to sign in</a>
    </p>
</div>

</body>
</html>
