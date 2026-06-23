<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Document Library')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="h-full flex flex-col">

    {{-- Navbar --}}
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- Brand --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-blue-700 font-bold text-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ config('app.name') }}
                </a>

                {{-- Search bar --}}
                <form action="{{ route('search') }}" method="GET" class="hidden md:flex flex-1 max-w-xl mx-6">
                    <div class="relative w-full">
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Search documents…"
                               class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </form>

                {{-- Right nav --}}
                <div class="flex items-center gap-3">

                    {{-- Notifications --}}
                    <a href="{{ route('notifications.index') }}" class="relative text-gray-500 hover:text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @php $unread = auth()->user()->notifications()->where('is_read', false)->count(); @endphp
                        @if($unread > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                                {{ $unread > 9 ? '9+' : $unread }}
                            </span>
                        @endif
                    </a>

                    {{-- User menu --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 text-sm text-gray-700 hover:text-blue-600 font-medium">
                            @php $navAvatar = auth()->user()->preferences?->avatar; @endphp
                            @if($navAvatar)
                                <img src="{{ asset('storage/' . $navAvatar) }}" alt="Avatar"
                                     class="w-8 h-8 rounded-full object-cover border border-gray-200">
                            @else
                                <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold uppercase">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="hidden md:block">{{ auth()->user()->name }}</span>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Browse Documents</a>
                            <a href="{{ route('favorites.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Favorites</a>
                            <a href="{{ route('reading-lists.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Reading Lists</a>
                            <a href="{{ route('history.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Recently Viewed</a>
                            <hr class="my-1 border-gray-100">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isEditor())
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-indigo-600 font-medium hover:bg-gray-50">Admin Panel</a>
                            @endif
                            <hr class="my-1 border-gray-100">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Sign Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash messages --}}
    @if(session('message'))
        <div class="max-w-7xl mx-auto px-4 pt-4 w-full">
            <div class="p-4 rounded-lg text-sm
                {{ session('status') === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' }}">
                {{ session('message') }}
            </div>
        </div>
    @endif

    {{-- Page content --}}
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 text-center text-xs text-gray-400 py-4">
        &copy; {{ date('Y') }} {{ config('app.name') }}
    </footer>

    @stack('scripts')
</body>
</html>
