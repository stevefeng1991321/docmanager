<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — {{ config('app.name') }} — @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .nav-scroll::-webkit-scrollbar { width: 4px; }
        .nav-scroll::-webkit-scrollbar-track { background: transparent; }
        .nav-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); border-radius: 99px; }
        .nav-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,.15); }
    </style>
    @stack('head')
</head>
<body class="h-full flex" x-data="{ sidebarOpen: false }">

    {{-- Mobile backdrop --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 lg:hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    {{-- ── Sidebar ─────────────────────────────────────────────────────────── --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-60 flex flex-col
                  transform transition-transform duration-200 ease-in-out
                  lg:relative lg:translate-x-0 lg:z-auto lg:flex-shrink-0"
           style="background:#0d1117;"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Logo --}}
        <div class="h-14 flex items-center px-4 flex-shrink-0" style="border-bottom:1px solid rgba(255,255,255,.06)">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 min-w-0">
                <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0 shadow-lg shadow-blue-900/40">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="font-semibold text-white text-sm tracking-tight truncate">DocManager</span>
                <span class="ml-auto text-[9px] font-medium px-1.5 py-0.5 rounded bg-blue-600/20 text-blue-400 border border-blue-500/20 flex-shrink-0">Admin</span>
            </a>
        </div>

        {{-- Nav --}}
        <nav class="nav-scroll flex-1 overflow-y-auto py-3 px-2.5">
            @php
                $pendingCount         = \App\Models\User::where('status', 'pending')->count();
                $pendingRequestsCount = \App\Models\AccountRequest::where('status', 'pending')->count();
                $failedJobs           = \DB::table('failed_jobs')->count();

                $groups = [
                    [
                        'label' => 'Overview',
                        'items' => [
                            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                            ['route' => 'chat.index',      'label' => 'Messages',  'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'live_badge' => true],
                        ],
                    ],
                    [
                        'label' => 'Content',
                        'items' => [
                            ['route' => 'admin.documents.index',       'label' => 'Documents',           'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['route' => 'admin.categories.index',      'label' => 'Categories',          'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z'],
                            ['route' => 'admin.tags.index',            'label' => 'Tags',                'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                            ['route' => 'admin.science-tech.index',    'label' => 'Science & Tech',      'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
                            ['route' => 'admin.basic-knowledge.index', 'label' => 'Basic Knowledge',     'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                        ],
                    ],
                    [
                        'label' => 'Developer & Testing',
                        'items' => [
                            ['route' => 'admin.problems.index', 'label' => 'Problems',        'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
                            ['route' => 'admin.tests.index',    'label' => 'Developer Tests', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                            ['route' => 'admin.compare.index',  'label' => 'Compare Docs',   'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                        ],
                    ],
                    [
                        'label' => 'People & HR',
                        'items' => [
                            ['route' => 'admin.users.index',            'label' => 'Users',            'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                            ['route' => 'admin.roles.index',            'label' => 'Roles',            'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                            ['route' => 'admin.employees.index',        'label' => 'Employees',        'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2.13a4 4 0 100-8 4 4 0 000 8zm6 0a4 4 0 100-8'],
                            ['route' => 'admin.account-requests.index', 'label' => 'Account Requests', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                            ['route' => 'admin.work-reports.index',     'label' => 'Work Reports',     'icon' => 'M9 17v-2a4 4 0 014-4h2m-6 6h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zm3-10h4'],
                            ['route' => 'admin.attendance.index',       'label' => 'Attendance',        'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['route' => 'admin.plans.index',            'label' => 'Plans',             'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                        ],
                    ],
                    [
                        'label' => 'Help & Documentation',
                        'items' => [
                            ['route' => 'admin.help',  'label' => 'Project Docs', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                            ['href' => 'https://laravel.com/docs',        'label' => 'Laravel',      'icon' => 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['href' => 'https://tailwindcss.com/docs',    'label' => 'Tailwind CSS', 'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01'],
                            ['href' => 'https://alpinejs.dev/start-here', 'label' => 'Alpine.js',   'icon' => 'M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5'],
                            ['href' => 'https://flowbite.com/docs/getting-started/introduction/', 'label' => 'Flowbite', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                            ['href' => 'https://vitejs.dev/guide/',       'label' => 'Vite',        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                        ],
                    ],
                    [
                        'label' => 'System',
                        'items' => [
                            ['route' => 'admin.audit-logs.index', 'label' => 'Audit Logs',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                            ['route' => 'admin.search.index',     'label' => 'Search Index', 'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                            ['route' => 'admin.storage.index',    'label' => 'Storage',      'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
                            ['route' => 'admin.backup.index',     'label' => 'DB Backup',    'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4'],
                            ['route' => 'admin.settings.index',   'label' => 'Settings',     'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                        ],
                    ],
                ];
            @endphp

            <div class="space-y-4">
            @foreach ($groups as $group)
                <div>
                    <p class="px-2 mb-1 text-[10px] font-semibold uppercase tracking-widest"
                       style="color:rgba(255,255,255,.28)">{{ $group['label'] }}</p>
                    <div class="space-y-0.5">
                    @foreach ($group['items'] as $item)
                        @if(empty($item['href']) && !Route::has($item['route'] ?? ''))
                            @continue
                        @endif
                        @php
                            $isExternal = !empty($item['href']);
                            $itemHref   = $isExternal ? $item['href'] : route($item['route']);
                            $active     = !$isExternal && request()->routeIs(
                                rtrim(preg_replace('/\.(index|show|edit|create)$/', '', $item['route']), '.') . '.*'
                            );
                        @endphp

                        @if(!empty($item['live_badge']))
                        {{-- Messages: live unread badge via Alpine --}}
                        <a href="{{ $itemHref }}"
                           @click="sidebarOpen = false"
                           x-data="{ unread: 0 }"
                           x-init="
                               fetch('{{ route('chat.unread-count') }}').then(r => r.json()).then(d => unread = d.unread_count);
                               window.Echo?.private('App.Models.User.{{ auth()->id() }}').listen('.message.sent', e => {
                                   if (e.sender_id !== {{ auth()->id() }}) unread++;
                               });
                           "
                           class="flex items-center gap-2.5 px-2 py-1.5 rounded-md text-[13px] font-medium transition-colors duration-100
                                  {{ $active ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white' }}"
                           style="{{ $active ? '' : 'hover:background:rgba(255,255,255,.05)' }}"
                           :style="!{{ $active ? 'true' : 'false' }} ? 'background:transparent' : ''"
                           @mouseenter="if (!{{ $active ? 'true' : 'false' }}) $el.style.background='rgba(255,255,255,.05)'"
                           @mouseleave="if (!{{ $active ? 'true' : 'false' }}) $el.style.background='transparent'">
                            <svg class="w-4 h-4 flex-shrink-0 {{ $active ? 'text-white' : 'text-gray-500' }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                            </svg>
                            <span class="truncate">{{ $item['label'] }}</span>
                            <span x-show="unread > 0" x-cloak
                                  class="ml-auto text-[10px] font-semibold bg-red-500 text-white rounded-full px-1.5 py-0.5 leading-none"
                                  x-text="unread > 9 ? '9+' : unread"></span>
                        </a>

                        @else
                        {{-- Regular nav item --}}
                        <a href="{{ $itemHref }}"
                           @click="sidebarOpen = false"
                           @if($isExternal) target="_blank" rel="noopener noreferrer" @endif
                           class="flex items-center gap-2.5 px-2 py-1.5 rounded-md text-[13px] font-medium transition-colors duration-100
                                  {{ $active ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                            <svg class="w-4 h-4 flex-shrink-0 {{ $active ? 'text-white' : 'text-gray-500' }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                            </svg>
                            <span class="truncate">{{ $item['label'] }}</span>
                            @php $itemRoute = $item['route'] ?? null; @endphp
                            @if($itemRoute === 'admin.users.index' && $pendingCount > 0)
                                <span class="ml-auto text-[10px] font-semibold bg-red-500 text-white rounded-full px-1.5 py-0.5 leading-none">{{ $pendingCount }}</span>
                            @elseif($itemRoute === 'admin.account-requests.index' && $pendingRequestsCount > 0)
                                <span class="ml-auto text-[10px] font-semibold bg-amber-500 text-white rounded-full px-1.5 py-0.5 leading-none">{{ $pendingRequestsCount }}</span>
                            @elseif($itemRoute === 'admin.jobs.index' && $failedJobs > 0)
                                <span class="ml-auto text-[10px] font-semibold bg-yellow-500 text-white rounded-full px-1.5 py-0.5 leading-none">{{ $failedJobs }}</span>
                            @endif
                        </a>
                        @endif

                    @endforeach
                    </div>
                </div>
            @endforeach
            </div>
        </nav>

        {{-- User footer --}}
        <div class="flex-shrink-0 p-2.5" style="border-top:1px solid rgba(255,255,255,.06)">
            <div class="flex items-center gap-2.5 px-2 py-2 rounded-md group">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center font-semibold uppercase text-white text-xs flex-shrink-0 shadow">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-medium text-white truncate leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] capitalize leading-tight" style="color:rgba(255,255,255,.35)">{{ auth()->user()->role }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0">
                    @csrf
                    <button type="submit" title="Sign out"
                            class="p-1 rounded transition-colors text-gray-600 hover:text-red-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── Main area ───────────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 h-14 flex items-center justify-between px-4 sm:px-6 flex-shrink-0">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-sm font-semibold text-gray-800 truncate">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-blue-600 transition-colors whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Client View
                </a>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('message'))
            <div x-data="{ show: true }"
                 x-show="show"
                 x-init="setTimeout(() => show = false, 4000)"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="mx-4 sm:mx-6 mt-4">
                <div class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-sm
                    {{ session('status') === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' }}">
                    <span>{{ session('message') }}</span>
                    <button @click="show = false" class="shrink-0 opacity-40 hover:opacity-100 transition-opacity leading-none text-lg">&times;</button>
                </div>
            </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
