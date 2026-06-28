<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center">

<div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-lg">

    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-600 text-white mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Forgot Password</h1>
        <p class="text-sm text-gray-500 mt-1">Submit a request — your administrator will provide a reset link.</p>
    </div>

    @if(session('message'))
        <div class="mb-5 p-4 rounded-lg bg-green-50 border border-green-200 text-sm text-green-800">
            {{ session('message') }}
        </div>
    @endif

    @if(!session('message'))
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">{{ __('auth.username_label') }}</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}"
                   required autofocus autocomplete="username"
                   class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-400 @else border-gray-300 @enderror">
            @error('username')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            {{ __('auth.submit_request') }}
        </button>
    </form>

    <div class="mt-5 p-4 rounded-lg bg-blue-50 border border-blue-100 text-xs text-blue-700 space-y-1">
        <p class="font-medium">How this works (offline mode)</p>
        <p>1. Submit your username above.</p>
        <p>2. Your administrator will see the request and generate a one-time reset link.</p>
        <p>3. They will hand you the link — use it to set a new password within 24 hours.</p>
    </div>
    @endif

    <p class="text-center text-sm text-gray-500 mt-6">
        <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">{{ __('auth.back_to_login') }}</a>
    </p>
</div>

</body>
</html>
