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
    @php
        $navAvatar = auth()->user()->preferences?->avatar;
        $navUnread = auth()->user()->notifications()->where('is_read', false)->count();
    @endphp
    <nav class="bg-white border-b border-gray-200 shadow-sm" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- Brand --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-blue-700 font-bold text-lg shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ config('app.name') }}
                </a>

                {{-- Search bar (desktop md+) --}}
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
                <div class="flex items-center gap-2 sm:gap-3">

                    {{-- Messages --}}
                    <a href="{{ route('chat.index') }}" class="relative text-gray-500 hover:text-blue-600"
                       x-data="{ unread: 0 }"
                       x-init="
                           fetch('{{ route('chat.unread-count') }}').then(r => r.json()).then(d => unread = d.unread_count);
                           window.Echo?.private('App.Models.User.{{ auth()->id() }}').listen('.message.sent', e => {
                               if (e.sender_id !== {{ auth()->id() }}) unread++;
                           });
                       ">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span x-show="unread > 0" x-cloak
                              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"
                              x-text="unread > 9 ? '9+' : unread"></span>
                    </a>

                    {{-- Notifications --}}
                    <a href="{{ route('notifications.index') }}" class="relative text-gray-500 hover:text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($navUnread > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                                {{ $navUnread > 9 ? '9+' : $navUnread }}
                            </span>
                        @endif
                    </a>

                    {{-- User dropdown (desktop md+) --}}
                    <div x-data="{ open: false }" class="relative hidden md:block">
                        <button @click="open = !open" class="flex items-center gap-2 text-sm text-gray-700 hover:text-blue-600 font-medium">
                            @if($navAvatar)
                                <img src="{{ asset('storage/' . $navAvatar) }}" alt="Avatar"
                                     class="w-8 h-8 rounded-full object-cover border border-gray-200">
                            @else
                                <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold uppercase">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                            <span>{{ auth()->user()->name }}</span>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Browse Documents</a>
                            <a href="{{ route('science-tech.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Science &amp; Technology</a>
                            <a href="{{ route('basic-knowledge.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Basic Knowledge</a>
                            <a href="{{ route('favorites.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Favorites</a>
                            <a href="{{ route('work-reports.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Work Reports</a>
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

                    {{-- Hamburger (mobile only) --}}
                    <button @click="mobileOpen = !mobileOpen"
                            class="md:hidden p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors"
                            :aria-expanded="mobileOpen">
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="md:hidden border-t border-gray-100 bg-white shadow-lg">
            <div class="px-4 pt-3 pb-4 space-y-3">

                {{-- Mobile search --}}
                <form action="{{ route('search') }}" method="GET">
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Search documents…"
                               class="w-full pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </form>

                {{-- User info strip --}}
                <div class="flex items-center gap-3 px-1 py-2 border-b border-gray-100">
                    @if($navAvatar)
                        <img src="{{ asset('storage/' . $navAvatar) }}" alt="Avatar"
                             class="w-9 h-9 rounded-full object-cover border border-gray-200 flex-shrink-0">
                    @else
                        <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold uppercase flex-shrink-0">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                {{-- Nav links --}}
                <nav class="space-y-0.5">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">Browse Documents</a>
                    <a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">Messages</a>
                    <a href="{{ route('science-tech.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">Science &amp; Technology</a>
                    <a href="{{ route('basic-knowledge.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">Basic Knowledge</a>
                    <a href="{{ route('favorites.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">Favorites</a>
                    <a href="{{ route('work-reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">Work Reports</a>
                    <a href="{{ route('attendance.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">Attendance</a>
                    <a href="{{ route('reading-lists.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">Reading Lists</a>
                    <a href="{{ route('history.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">Recently Viewed</a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">Profile</a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isEditor())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-indigo-600 font-medium hover:bg-indigo-50 transition-colors">Admin Panel</a>
                    @endif
                </nav>

                <div class="border-t border-gray-100 pt-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2.5 rounded-lg text-sm text-red-600 hover:bg-red-50 transition-colors">Sign Out</button>
                    </form>
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
