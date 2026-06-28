<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Document Library')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
    {{-- i18n: translations baked into HTML so they work fully offline --}}
    <script>
    window.LANG = @json(trans('js'));
    window.__ = function(key, replace) {
        var parts = key.split('.');
        var v = window.LANG || {};
        for (var i = 0; i < parts.length; i++) {
            if (v === undefined || v === null || typeof v !== 'object') return key;
            v = v[parts[i]];
        }
        if (typeof v !== 'string') return key;
        if (replace) {
            Object.keys(replace).forEach(function(k) { v = v.replace(':' + k, replace[k]); });
        }
        return v;
    };
    </script>
</head>
<body class="h-full flex flex-col">

    {{-- Navbar --}}
    @php
        $navAvatar = auth()->user()->preferences?->avatar;
        $navUnread = auth()->user()->notifications()->where('is_read', false)->count();
        $navChatUnread = \App\Models\Conversation::forUser(auth()->id())
            ->whereHas('participants', fn($q) => $q->where('user_id', auth()->id())
                ->where(fn($q2) => $q2->whereNull('last_read_at')
                    ->orWhereColumn('last_read_at', '<', 'conversations.last_message_at')))
            ->whereNotNull('last_message_at')
            ->count();
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
                               placeholder="{{ __('ui.nav_search_placeholder') }}"
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

                    {{-- Chat (with live notification badge + toasts) --}}
                    <div x-data="chatNotify()" x-init="init()" class="relative">
                        <a href="{{ route('chat.index') }}" class="relative text-gray-500 hover:text-blue-600 block" title="Messages">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <template x-if="badge > 0">
                                <span class="absolute -top-1 -right-1 bg-blue-600 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"
                                      x-text="badge > 9 ? '9+' : badge"></span>
                            </template>
                        </a>

                        {{-- Toast stack (fixed, bottom-right) --}}
                        <div class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 pointer-events-none">
                            <template x-for="t in toasts" :key="t.id">
                                <a :href="'/chat/' + t.conversation_id"
                                   @click="dismiss(t.id)"
                                   class="pointer-events-auto flex items-start gap-3 bg-gray-900 text-white px-4 py-3 rounded-xl shadow-xl w-72 hover:bg-gray-800 transition cursor-pointer"
                                   x-transition:enter="transition ease-out duration-300"
                                   x-transition:enter-start="opacity-0 translate-y-4"
                                   x-transition:enter-end="opacity-100 translate-y-0"
                                   x-transition:leave="transition ease-in duration-200"
                                   x-transition:leave-start="opacity-100 translate-y-0"
                                   x-transition:leave-end="opacity-0 translate-y-4"
                                >
                                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center font-bold text-sm flex-shrink-0"
                                         x-text="t.sender_name.charAt(0).toUpperCase()"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold leading-tight" x-text="t.sender_name"></p>
                                        <p class="text-xs text-gray-300 truncate mt-0.5" x-text="t.body"></p>
                                    </div>
                                    <button @click.prevent.stop="dismiss(t.id)"
                                            class="text-gray-400 hover:text-white flex-shrink-0 mt-0.5 -mr-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </a>
                            </template>
                        </div>
                    </div>

                    {{-- Language switcher --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="p-1.5 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-gray-100 transition"
                                :title="__('ui.language') || '{{ __('ui.language') }}'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 mt-2 w-36 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <p class="px-3 py-1.5 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">{{ __('ui.language') }}</p>
                            @foreach(\App\Http\Middleware\SetLocale::SUPPORTED as $loc)
                                <form action="{{ route('locale.switch') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="locale" value="{{ $loc }}">
                                    <button type="submit"
                                            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 transition flex items-center gap-2
                                                   {{ app()->getLocale() === $loc ? 'font-semibold text-blue-600' : 'text-gray-700' }}">
                                        @if($loc === 'en') 🇬🇧 @else 🇨🇳 @endif
                                        {{ __('ui.locale_' . $loc) }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>

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
                            <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('ui.nav_browse') }}</a>
                            <a href="{{ route('science-tech.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('ui.nav_science_tech') }}</a>
                            <a href="{{ route('basic-knowledge.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('ui.nav_basic_knowledge') }}</a>
                            <a href="{{ route('favorites.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('ui.nav_favorites') }}</a>
                            <a href="{{ route('work-reports.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('ui.nav_work_reports') }}</a>
                            <a href="{{ route('attendance.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('ui.nav_attendance') }}</a>
                            <a href="{{ route('plans.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('ui.nav_my_plans') }}</a>
                            <a href="{{ route('chat.index') }}" class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                {{ __('ui.nav_messages') }}
                                @if($navChatUnread > 0)
                                    <span class="min-w-[18px] h-[18px] rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center px-1">{{ $navChatUnread }}</span>
                                @endif
                            </a>
                            <a href="{{ route('reading-lists.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('ui.nav_reading_lists') }}</a>
                            <a href="{{ route('history.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('ui.nav_recently_viewed') }}</a>
                            <hr class="my-1 border-gray-100">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('ui.nav_profile') }}</a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isEditor())
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-indigo-600 font-medium hover:bg-gray-50">{{ __('ui.nav_admin_panel') }}</a>
                            @endif
                            <hr class="my-1 border-gray-100">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">{{ __('ui.nav_sign_out') }}</button>
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
                               placeholder="{{ __('ui.nav_search_placeholder') }}"
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
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">{{ __('ui.nav_browse') }}</a>
                    <a href="{{ route('science-tech.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">{{ __('ui.nav_science_tech') }}</a>
                    <a href="{{ route('basic-knowledge.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">{{ __('ui.nav_basic_knowledge') }}</a>
                    <a href="{{ route('favorites.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">{{ __('ui.nav_favorites') }}</a>
                    <a href="{{ route('work-reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">{{ __('ui.nav_work_reports') }}</a>
                    <a href="{{ route('attendance.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">{{ __('ui.nav_attendance') }}</a>
                    <a href="{{ route('plans.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">{{ __('ui.nav_my_plans') }}</a>
                    <a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        {{ __('ui.nav_messages') }}
                        @if($navChatUnread > 0)
                            <span class="ml-auto min-w-[18px] h-[18px] rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center px-1">{{ $navChatUnread }}</span>
                        @endif
                    </a>
                    <a href="{{ route('reading-lists.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">{{ __('ui.nav_reading_lists') }}</a>
                    <a href="{{ route('history.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">{{ __('ui.nav_recently_viewed') }}</a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">{{ __('ui.nav_profile') }}</a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isEditor())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-indigo-600 font-medium hover:bg-indigo-50 transition-colors">{{ __('ui.nav_admin_panel') }}</a>
                    @endif
                </nav>

                <div class="border-t border-gray-100 pt-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2.5 rounded-lg text-sm text-red-600 hover:bg-red-50 transition-colors">{{ __('ui.nav_sign_out') }}</button>
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

    <script>
    function chatNotify() {
        return {
            badge: {{ (int) $navChatUnread }},
            toasts: [],
            _seq: 0,
            _seen: new Set(),

            init() {
                if (!window.Echo) return;
                const ch = window.Echo.private('user.{{ auth()->id() }}');
                ch.stopListening('.new.message');
                ch.listen('.new.message', (e) => {
                    // Deduplicate — guard against double delivery or double registration
                    if (e.message_id && this._seen.has(e.message_id)) return;
                    if (e.message_id) this._seen.add(e.message_id);

                    // suppress toast if already viewing that conversation
                    if (window.location.pathname === '/chat/' + e.conversation_id) return;

                    this.badge++;
                    const id = ++this._seq;
                    this.toasts.push({
                        id,
                        conversation_id: e.conversation_id,
                        sender_name: e.sender_name,
                        body: e.body,
                    });
                    setTimeout(() => this.dismiss(id), 5000);
                    // Let other page components (e.g. chat index list) react
                    window.dispatchEvent(new CustomEvent('chat:new-message', { detail: e }));
                });
            },

            dismiss(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            },
        };
    }
    </script>
</body>
</html>
