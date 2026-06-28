<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $resource->title }} — {{ __('share.heading') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

<div class="bg-white rounded-2xl shadow-lg border border-gray-100 max-w-md w-full p-8 text-center space-y-4">
    <div class="text-4xl">&#128196;</div>
    <h1 class="text-xl font-bold text-gray-900">{{ $resource->title }}</h1>
    <p class="text-sm text-gray-500">{{ $resource->category?->name ?? __('categories.all') }}</p>

    @if($resource->description)
    <p class="text-sm text-gray-600 leading-relaxed">{{ $resource->description }}</p>
    @endif

    <div class="text-xs text-gray-400">
        {{ strtoupper($resource->file_type) }} &middot; {{ number_format($resource->file_size / 1024) }} KB
    </div>

    <p class="text-xs text-orange-500">
        This link expires {{ $share->expires_at->diffForHumans() }}.
    </p>

    <div class="flex flex-col gap-2 pt-2">
        @auth
        <a href="{{ route('documents.download', $resource) }}"
           class="block w-full px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            {{ __('share.download_button') }}
        </a>
        <a href="{{ route('documents.show', $resource) }}"
           class="block w-full px-5 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
            {{ __('share.view_button') }}
        </a>
        @else
        <a href="{{ route('login') }}"
           class="block w-full px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            {{ __('share.submit_password') }}
        </a>
        @endauth
    </div>
</div>

</body>
</html>
