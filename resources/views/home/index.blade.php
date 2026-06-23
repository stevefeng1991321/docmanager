@extends('layouts.app')
@section('title', 'Technical Library')

@section('content')
<div
    class="flex gap-6"
    x-data="catBrowser()"
    @click.capture="handlePaginationClick($event)"
>

    {{-- ── Sidebar: category tree ──────────────────────────────────────── --}}
    <aside class="hidden lg:block w-56 flex-shrink-0">
        <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Browse by Category</h2>
        <nav class="space-y-0.5">

            {{-- All Documents --}}
            <button
                @click="select(null, 'All Documents')"
                :class="active === null ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-100'"
                class="w-full flex items-center px-3 py-2 rounded-lg text-sm text-left transition-colors"
            >
                All Documents
            </button>

            {{-- Parent categories --}}
            <template x-for="cat in cats" :key="cat.id">
                <div>
                    <div class="flex items-stretch">
                        {{-- Expand chevron (only shown when children exist) --}}
                        <button
                            x-show="cat.children && cat.children.length"
                            @click.stop="open[cat.id] = !open[cat.id]"
                            class="flex items-center justify-center w-6 ml-1 text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0"
                            :aria-label="open[cat.id] ? 'Collapse' : 'Expand'"
                        >
                            <svg
                                class="w-3 h-3 transition-transform duration-150"
                                :class="open[cat.id] ? 'rotate-90' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        {{-- Spacer when no chevron --}}
                        <span x-show="!cat.children || !cat.children.length" class="w-7 flex-shrink-0"></span>

                        {{-- Category name button --}}
                        <button
                            @click="select(cat.slug, cat.name)"
                            :class="active === cat.slug ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-100'"
                            class="flex-1 flex items-center justify-between px-2 py-2 rounded-lg text-sm text-left transition-colors min-w-0"
                        >
                            <span class="truncate" x-text="cat.name"></span>
                            <span class="text-xs text-gray-400 ml-1 flex-shrink-0" x-text="cat.resources_count ?? cat.count ?? 0"></span>
                        </button>
                    </div>

                    {{-- Children --}}
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

        {{-- Controls bar --}}
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h2 class="text-base font-semibold text-gray-800" x-text="activeName"></h2>
            <div class="flex items-center gap-2">
                {{-- Sort --}}
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

                {{-- View toggle --}}
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
            {{-- Spinner --}}
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

            {{-- Document area (initial render, replaced on category change) --}}
            <div x-ref="docsArea">
                @include('home._docs')
            </div>
        </div>

    </div>
</div>

<script>
function catBrowser() {
    return {
        cats: @json($categories),
        open: {},
        active: null,
        activeName: 'All Documents',
        loading: false,
        sort: '{{ $sort }}',
        view: '{{ $view }}',

        init() {
            this.cats.forEach(cat => {
                this.open[cat.id] = false;
            });
        },

        select(slug, name) {
            this.active = slug;
            this.activeName = name ?? 'All Documents';
            this.load();
        },

        load(page) {
            this.loading = true;
            const params = new URLSearchParams({ sort: this.sort, view: this.view });
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
@endsection
