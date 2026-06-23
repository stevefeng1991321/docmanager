<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center">

<div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-lg">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-600 text-white mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">{{ config('app.name') }}</h1>
        <p class="text-sm text-gray-500 mt-1">Sign in to your account</p>
    </div>

    {{-- Status messages --}}
    @if(session('message'))
        <div class="mb-4 p-3 rounded-lg text-sm
            {{ session('status') === 'pending' || session('status') === 'inactive' ? 'bg-yellow-50 text-yellow-800 border border-yellow-200' : 'bg-green-50 text-green-800 border border-green-200' }}">
            {{ session('message') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}"
                   required autofocus autocomplete="username"
                   class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-400 @else border-gray-300 @enderror">
            @error('username')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password"
                   class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-400 @else border-gray-300 @enderror">
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600">
                Remember me
            </label>
        </div>

        <button type="submit"
                class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            Sign In
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-medium">Create one</a>
    </p>
</div>

</body>
</html>
