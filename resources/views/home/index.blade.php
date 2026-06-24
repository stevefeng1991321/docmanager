@extends('layouts.app')
@section('title', 'Technical Library')

@section('content')
<div x-data="catBrowser()" @click.capture="handlePaginationClick($event)">
<div class="flex gap-6">

    {{-- ── Sidebar: category tree (desktop lg+) ───────────────────────────── --}}
    <aside class="hidden lg:block w-56 flex-shrink-0">
        <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Browse by Category</h2>
        <nav class="space-y-0.5">

            <button
                @click="select(null, 'All Documents')"
                :class="active === null ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-100'"
                class="w-full flex items-center px-3 py-2 rounded-lg text-sm text-left transition-colors"
            >
                All Documents
            </button>

            <template x-for="cat in cats" :key="cat.id">
                <div>
                    <div class="flex items-stretch">
                        <button
                            x-show="cat.children && cat.children.length"
                            @click.stop="open[cat.id] = !open[cat.id]"
                            class="flex items-center justify-center w-6 ml-1 text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0"
                        >
                            <svg class="w-3 h-3 transition-transform duration-150"
                                 :class="open[cat.id] ? 'rotate-90' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <span x-show="!cat.children || !cat.children.length" class="w-7 flex-shrink-0"></span>
                        <button
                            @click="select(cat.slug, cat.name)"
                            :class="active === cat.slug ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-100'"
                            class="flex-1 flex items-center justify-between px-2 py-2 rounded-lg text-sm text-left transition-colors min-w-0"
                        >
                            <span class="truncate" x-text="cat.name"></span>
                            <span class="text-xs text-gray-400 ml-1 flex-shrink-0" x-text="cat.resources_count ?? cat.count ?? 0"></span>
                        </button>
                    </div>

                    <div
                        x-show="open[cat.id]"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="ml-7 mt-0.5 space-y-0.5"
                    >
                        <template x-for="child in cat.children" :key="child.id">
                            <button
                                @click="select(child.slug, child.name)"
                                :class="active === child.slug ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-500 hover:bg-gray-100'"
                                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-xs text-left transition-colors"
                            >
                                <span class="truncate" x-text="child.name"></span>
                                <span class="text-gray-400 ml-1 flex-shrink-0" x-text="child.resources_count ?? child.count ?? 0"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

        </nav>
    </aside>

    {{-- ── Main content ─────────────────────────────────────────────────── --}}
    <div class="flex-1 min-w-0 space-y-4">

        {{-- Mobile: category filter button (< lg) --}}
        <div class="lg:hidden">
            <button @click="drawerOpen = true"
                    class="flex items-center gap-2 w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                <span>Browse by Category</span>
                <span x-show="active !== null"
                      class="ml-auto text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium flex-shrink-0"
                      x-text="activeName"></span>
                <svg class="w-4 h-4 text-gray-400 ml-auto flex-shrink-0" x-show="active === null" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Controls bar --}}
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h2 class="text-base font-semibold text-gray-800" x-text="activeName"></h2>
            <div class="flex items-center gap-2">
                <select
                    x-model="sort"
                    @change="load()"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
                >
                    <option value="date_desc">Newest first</option>
                    <option value="date_asc">Oldest first</option>
                    <option value="name_asc">A → Z</option>
                    <option value="name_desc">Z → A</option>
                    <option value="downloads">Most downloaded</option>
                    <option value="size_desc">Largest first</option>
                </select>

                <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                    <button
                        @click="view = 'grid'; load()"
                        :class="view === 'grid' ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50'"
                        class="px-3 py-1.5 text-sm transition-colors"
                        title="Grid view"
                    >⊞</button>
                    <button
                        @click="view = 'list'; load()"
                        :class="view === 'list' ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50'"
                        class="px-3 py-1.5 text-sm border-l border-gray-300 transition-colors"
                        title="List view"
                    >☰</button>
                </div>
            </div>
        </div>

        {{-- Loading overlay + content --}}
        <div class="relative">
            <div
                x-show="loading"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="absolute inset-0 bg-white/70 flex items-center justify-center z-10 rounded-xl"
                style="min-height: 200px;"
            >
                <svg class="animate-spin w-7 h-7 text-blue-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4z"/>
                </svg>
            </div>

            <div x-ref="docsArea">
                @include('home._docs')
            </div>
        </div>

    </div>
</div>{{-- end flex --}}

{{-- ── Mobile category drawer ──────────────────────────────────────────── --}}
{{-- Backdrop --}}
<div x-show="drawerOpen" x-cloak @click="drawerOpen = false"
     class="fixed inset-0 bg-black/50 z-40 lg:hidden"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
</div>

{{-- Drawer panel --}}
<div x-show="drawerOpen" x-cloak
     class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-xl flex flex-col lg:hidden"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full">

    <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200 flex-shrink-0">
        <h2 class="font-semibold text-gray-800 text-sm">Browse by Category</h2>
        <button @click="drawerOpen = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto p-3 space-y-0.5">
        <button @click="select(null, 'All Documents'); drawerOpen = false"
                :class="active === null ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-100'"
                class="w-full flex items-center px-3 py-2.5 rounded-lg text-sm text-left transition-colors">
            All Documents
        </button>

        <template x-for="cat in cats" :key="'d-' + cat.id">
            <div>
                <div class="flex items-stretch">
                    <button x-show="cat.children && cat.children.length"
                            @click.stop="open[cat.id] = !open[cat.id]"
                            class="flex items-center justify-center w-7 ml-1 text-gray-400 hover:text-gray-600 flex-shrink-0">
                        <svg class="w-3 h-3 transition-transform duration-150"
                             :class="open[cat.id] ? 'rotate-90' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <span x-show="!cat.children || !cat.children.length" class="w-8 flex-shrink-0"></span>
                    <button @click="select(cat.slug, cat.name); drawerOpen = false"
                            :class="active === cat.slug ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-100'"
                            class="flex-1 flex items-center justify-between px-2 py-2.5 rounded-lg text-sm text-left transition-colors min-w-0">
                        <span class="truncate" x-text="cat.name"></span>
                        <span class="text-xs text-gray-400 ml-1 flex-shrink-0" x-text="cat.resources_count ?? cat.count ?? 0"></span>
                    </button>
                </div>
                <div x-show="open[cat.id]" class="ml-8 mt-0.5 space-y-0.5">
                    <template x-for="child in cat.children" :key="'dc-' + child.id">
                        <button @click="select(child.slug, child.name); drawerOpen = false"
                                :class="active === child.slug ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-500 hover:bg-gray-100'"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs text-left transition-colors">
                            <span class="truncate" x-text="child.name"></span>
                            <span class="text-gray-400 ml-1 flex-shrink-0" x-text="child.resources_count ?? child.count ?? 0"></span>
                        </button>
                    </template>
                </div>
            </div>
        </template>
    </nav>
</div>

<script>
function catBrowser() {
    return {
        cats: @json($categories),
        open: {},
        active: null,
        activeName: 'All Documents',
        loading: false,
        drawerOpen: false,
        sort: '{{ $sort }}',
        view: '{{ $view }}',
        perPage: {{ $perPage }},

        init() {
            this.cats.forEach(cat => {
                this.open[cat.id] = false;
            });
            document.addEventListener('per-page-change', (e) => {
                this.perPage = e.detail.perPage;
                this.load();
            });
        },

        select(slug, name) {
            this.active = slug;
            this.activeName = name ?? 'All Documents';
            this.load();
        },

        load(page) {
            this.loading = true;
            const params = new URLSearchParams({ sort: this.sort, view: this.view, per_page: this.perPage });
            if (this.active) params.set('category', this.active);
            if (page) params.set('page', page);

            fetch('{{ route("home.browse") }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.text())
            .then(html => {
                this.$refs.docsArea.innerHTML = html;
                this.loading = false;
                this.$refs.docsArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch(() => { this.loading = false; });
        },

        handlePaginationClick(e) {
            const link = e.target.closest('[data-pagination] a');
            if (!link) return;
            e.preventDefault();
            const url = new URL(link.href);
            this.load(url.searchParams.get('page'));
        }
    };
}
</script>
</div>{{-- end x-data catBrowser --}}
@endsection
