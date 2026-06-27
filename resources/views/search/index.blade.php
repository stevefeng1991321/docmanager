@extends('layouts.app')
@section('title', $query ? "Search: {$query}" : 'Search')

@section('content')
<div class="flex gap-6" x-data="{ filtersOpen: false }">

    {{-- Filter sidebar (desktop lg+) --}}
    <aside class="hidden lg:block w-52 flex-shrink-0">
        <form method="GET" action="{{ route('search') }}" id="filter-form" class="space-y-5">
            <input type="hidden" name="q" value="{{ $query }}">
            <input type="hidden" name="mode" value="{{ $mode }}">

            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">File Type</h3>
                <p class="text-xs text-gray-400 mb-1.5">Documents only</p>
                @foreach (['pdf','docx','xlsx','pptx','txt','png','jpg'] as $ft)
                <label class="flex items-center gap-2 py-0.5 text-sm text-gray-700 cursor-pointer hover:text-blue-600">
                    <input type="radio" name="type" value="{{ $ft }}" {{ $type === $ft ? 'checked' : '' }}
                           onchange="document.getElementById('filter-form').submit()"
                           class="text-blue-600">
                    {{ strtoupper($ft) }}
                </label>
                @endforeach
                @if($type)
                <a href="{{ route('search', array_merge(request()->query(), ['type' => null])) }}"
                   class="text-xs text-blue-500 hover:underline mt-1 block">Clear type</a>
                @endif
            </div>

            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Category</h3>
                <select name="category_id" onchange="document.getElementById('filter-form').submit()"
                        class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                    <option value="">All categories</option>
                    @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Upload Date</h3>
                <div class="space-y-1.5">
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                           onchange="document.getElementById('filter-form').submit()"
                           class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs"
                           placeholder="From">
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                           onchange="document.getElementById('filter-form').submit()"
                           class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs"
                           placeholder="To">
                </div>
            </div>

            @if($type || $categoryId || $dateFrom || $dateTo)
            <a href="{{ route('search', ['q' => $query, 'mode' => $mode]) }}"
               class="block text-center text-xs text-red-500 hover:underline border border-red-200 rounded-lg py-1.5">
                Clear all filters
            </a>
            @endif
        </form>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 min-w-0 space-y-4">

        {{-- Mobile: filter toggle button (< lg) --}}
        <div class="lg:hidden">
            <button @click="filtersOpen = true"
                    class="flex items-center gap-2 px-4 py-2.5 border border-gray-300 rounded-xl text-sm text-gray-700 hover:bg-gray-50 transition-colors w-full">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                <span>Filters</span>
                @if($type || $categoryId || $dateFrom || $dateTo)
                <span class="ml-auto bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full font-medium">Active</span>
                @endif
            </button>
        </div>

        {{-- Search form --}}
        <form method="GET" action="{{ route('search') }}" class="space-y-3"
              x-data="searchAutocomplete('{{ addslashes($query) }}')"
              @submit="open = false">
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="category_id" value="{{ $categoryId }}">
            <input type="hidden" name="date_from" value="{{ $dateFrom }}">
            <input type="hidden" name="date_to" value="{{ $dateTo }}">

            <div class="flex gap-2 relative">
                <div class="flex-1 relative">
                    <input type="text" name="q" x-model="term" placeholder="Search documents…" autofocus
                           @input.debounce.300ms="fetchSuggestions()"
                           @keydown.escape="open = false"
                           @keydown.arrow-down.prevent="focusSuggestion(1)"
                           @keydown.arrow-up.prevent="focusSuggestion(-1)"
                           @keydown.enter="selectFocused($event)"
                           @focus="term.length >= 2 && suggestions.length && (open = true)"
                           @click.outside="open = false"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none"
                           autocomplete="off">

                    {{-- Autocomplete dropdown --}}
                    <ul x-show="open && suggestions.length" x-cloak
                        class="absolute z-50 left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                        <template x-for="(s, i) in suggestions" :key="i">
                            <li @click="choose(s)"
                                :class="focused === i ? 'bg-blue-50' : 'hover:bg-gray-50'"
                                class="px-4 py-2.5 text-sm text-gray-700 cursor-pointer flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <span x-text="s" class="truncate"></span>
                            </li>
                        </template>
                    </ul>
                </div>

                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition flex-shrink-0">
                    Search
                </button>
                @if($query)
                <a href="{{ route('search') }}" class="px-4 py-2.5 border border-gray-300 text-gray-500 hover:bg-gray-50 text-sm rounded-xl transition flex-shrink-0">
                    Clear
                </a>
                @endif
            </div>

            {{-- Mode toggle --}}
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400 font-medium uppercase tracking-wide">Mode</span>

                {{-- Keyword button --}}
                <button type="submit" name="mode" value="keyword"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border transition-all
                               {{ $mode === 'keyword'
                                   ? 'bg-blue-600 border-blue-600 text-white shadow-sm'
                                   : 'bg-white border-gray-300 text-gray-500 hover:border-gray-400 hover:text-gray-700' }}">
                    @if($mode === 'keyword')
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @endif
                    Keyword
                </button>

                {{-- AI Semantic button --}}
                <button type="submit" name="mode" value="ai"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border transition-all
                               {{ $mode === 'ai'
                                   ? 'bg-purple-600 border-purple-600 text-white shadow-sm'
                                   : 'bg-white border-gray-300 text-gray-500 hover:border-gray-400 hover:text-gray-700' }}">
                    @if($mode === 'ai')
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @endif
                    ✦ AI Semantic
                </button>

                {{-- Hybrid button --}}
                <button type="submit" name="mode" value="hybrid"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border transition-all
                               {{ $mode === 'hybrid'
                                   ? 'bg-teal-600 border-teal-600 text-white shadow-sm'
                                   : 'bg-white border-gray-300 text-gray-500 hover:border-gray-400 hover:text-gray-700' }}">
                    @if($mode === 'hybrid')
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @endif
                    ⚡ Hybrid
                </button>

                @if($mode === 'ai')
                <span class="text-xs text-purple-600">Finds conceptually related documents</span>
                @elseif($mode === 'hybrid')
                <span class="text-xs text-teal-600">Blends keyword + semantic scoring</span>
                @endif
            </div>
        </form>

        {{-- Index missing notice --}}
        @if($indexMissing)
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-start gap-3">
            <span class="text-amber-500 text-lg mt-0.5">⚠</span>
            <div class="text-sm text-amber-800">
                <strong>AI search index not built yet.</strong>
                Ask an admin to build it from the
                <a href="{{ route('admin.search.index') }}" class="underline hover:text-amber-900">Search Analytics</a> page,
                or run: <code class="bg-amber-100 px-1 rounded font-mono text-xs">php artisan search:build-tfidf</code>
            </div>
        </div>
        @endif

        @if($query)
        {{-- Results header --}}
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-3">
                <p class="text-sm text-gray-500">
                    @if($total)
                        <strong>{{ number_format($total) }}</strong> result{{ $total !== 1 ? 's' : '' }} for "<strong>{{ $query }}</strong>"
                        @if($mode === 'ai')
                        <span class="text-purple-600">&mdash; AI ranked</span>
                        @elseif($mode === 'hybrid')
                        <span class="text-teal-600">&mdash; Hybrid ranked</span>
                        @endif
                    @else
                        No results for "<strong>{{ $query }}</strong>"
                    @endif
                </p>
                @if($total > 0)
                <form method="POST" action="{{ route('saved-searches.store') }}" class="inline">
                    @csrf
                    <input type="hidden" name="query" value="{{ $query }}">
                    <button class="text-xs text-blue-600 hover:underline border border-blue-200 px-2 py-0.5 rounded">Save search</button>
                </form>
                @endif
            </div>

            {{-- Sort (keyword mode only) --}}
            @if($mode === 'keyword')
            <form method="GET" action="{{ route('search') }}">
                <input type="hidden" name="q" value="{{ $query }}">
                <input type="hidden" name="mode" value="keyword">
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="category_id" value="{{ $categoryId }}">
                <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                <input type="hidden" name="date_to" value="{{ $dateTo }}">
                <select name="sort" onchange="this.form.submit()"
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-gray-700">
                    <option value="relevance" {{ $sort === 'relevance' ? 'selected' : '' }}>Most relevant</option>
                    <option value="date_desc" {{ $sort === 'date_desc' ? 'selected' : '' }}>Newest first</option>
                    <option value="date_asc"  {{ $sort === 'date_asc'  ? 'selected' : '' }}>Oldest first</option>
                    <option value="name_asc"  {{ $sort === 'name_asc'  ? 'selected' : '' }}>A → Z</option>
                    <option value="downloads" {{ $sort === 'downloads' ? 'selected' : '' }}>Most downloaded</option>
                    <option value="size_desc" {{ $sort === 'size_desc' ? 'selected' : '' }}>Largest first</option>
                </select>
            </form>
            @elseif($mode === 'hybrid')
            <span class="text-xs text-gray-400 italic">Sorted by hybrid score</span>
            @else
            <span class="text-xs text-gray-400 italic">Sorted by semantic similarity</span>
            @endif
        </div>

        {{-- Results list --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">
            @forelse ($results as $doc)
            @php
                $hl = function(string $text) use ($query): string {
                    if (!$query) return e($text);
                    $words   = array_filter(explode(' ', $query), fn($w) => mb_strlen($w) >= 3);
                    $terms   = array_unique(array_merge([preg_quote($query, '/')], array_map(fn($w) => preg_quote($w, '/'), $words)));
                    $pattern = '/(' . implode('|', $terms) . ')/iu';
                    return preg_replace($pattern, '<mark class="bg-yellow-100 text-yellow-800 rounded px-0.5">$1</mark>', e($text));
                };
                $similarity = $scores[$doc->_key] ?? null;
                $snippet    = $snippets[$doc->_key] ?? null;
                $isDoc      = $doc->_type === 'document';
                $url        = $isDoc
                    ? route('documents.show', $doc->id)
                    : route('basic-knowledge.show', $doc->id);
            @endphp
            <a href="{{ $url }}" class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        {{-- Result type badge --}}
                        @if($isDoc)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-600 border border-blue-100 flex-shrink-0">Doc</span>
                        @else
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-600 border border-indigo-100 flex-shrink-0">Knowledge</span>
                        @endif

                        <p class="font-semibold text-gray-800 text-sm">{!! $hl($doc->title) !!}</p>

                        @if($similarity !== null)
                        <span class="text-xs font-medium border px-2 py-0.5 rounded-full flex-shrink-0
                            {{ $mode === 'hybrid' ? 'text-teal-700 bg-teal-50 border-teal-100' : 'text-purple-700 bg-purple-50 border-purple-100' }}">
                            {{ round($similarity * 100) }}% match
                        </span>
                        @endif
                    </div>

                    @if($snippet)
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{!! $hl($snippet) !!}</p>
                    @elseif($isDoc && $doc->description)
                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{!! $hl($doc->description) !!}</p>
                    @elseif(!$isDoc && $doc->summary)
                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{!! $hl($doc->summary) !!}</p>
                    @endif

                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-400 flex-wrap">
                        <span>{{ $doc->category?->name ?? 'Uncategorised' }}</span>
                        @if($isDoc)
                        <span>{{ number_format($doc->download_count) }} downloads</span>
                        @endif
                        <span>{{ $doc->created_at->format('Y-m-d') }}</span>
                        @if($isDoc && $doc->ratings_avg_rating)
                        <span class="text-yellow-500">&#9733; {{ number_format($doc->ratings_avg_rating, 1) }}</span>
                        @endif
                    </div>
                </div>
                @if($isDoc)
                <span class="flex-shrink-0 text-xs text-gray-400 uppercase font-mono bg-gray-100 px-2 py-0.5 rounded">
                    {{ $doc->file_type }}
                </span>
                @endif
            </a>
            @empty
            <div class="px-5 py-10 text-center text-gray-400 text-sm">
                <p class="text-3xl mb-2">&#128269;</p>
                <p>No results found for "<strong class="text-gray-600">{{ $query }}</strong>".</p>
                @if($mode === 'ai')
                <p class="mt-1">Try switching to <strong>Keyword</strong> mode, or make sure documents have extractable content.</p>
                @else
                <p class="mt-1">Try different keywords or <a href="{{ route('home') }}" class="text-blue-500 hover:underline">browse by category</a>.</p>
                @endif
            </div>
            @endforelse
        </div>

        @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div>{{ $results->links() }}</div>
        @endif

        @else
        {{-- Empty state: saved searches --}}
        @if($savedSearches->count())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <h3 class="text-xs font-semibold uppercase text-gray-500 mb-2">Saved Searches</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($savedSearches as $saved)
                <a href="{{ route('search', ['q' => $saved->query, 'mode' => $mode]) }}"
                   class="px-3 py-1 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 rounded-full text-sm transition">
                    {{ $saved->query }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
        @endif

    </div>
</div>

@push('scripts')
<script>
function searchAutocomplete(initialTerm) {
    return {
        term: initialTerm,
        suggestions: [],
        open: false,
        focused: -1,
        _timer: null,

        async fetchSuggestions() {
            this.focused = -1;
            if (this.term.length < 2) { this.suggestions = []; this.open = false; return; }
            const res = await fetch('{{ route('search.suggest') }}?q=' + encodeURIComponent(this.term));
            if (!res.ok) return;
            this.suggestions = await res.json();
            this.open = this.suggestions.length > 0;
        },

        focusSuggestion(dir) {
            if (!this.open) return;
            this.focused = Math.max(-1, Math.min(this.suggestions.length - 1, this.focused + dir));
        },

        selectFocused(e) {
            if (this.focused >= 0 && this.open) {
                e.preventDefault();
                this.choose(this.suggestions[this.focused]);
            }
        },

        choose(s) {
            this.term = s;
            this.open = false;
            this.$nextTick(() => this.$el.submit());
        },
    };
}
</script>
@endpush

{{-- Mobile filter drawer --}}
<div x-show="filtersOpen" x-cloak @click="filtersOpen = false"
     class="fixed inset-0 bg-black/50 z-40 lg:hidden"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
</div>

<div x-show="filtersOpen" x-cloak
     class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-xl flex flex-col lg:hidden"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     @click.stop>

    <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200 flex-shrink-0">
        <h2 class="font-semibold text-gray-800 text-sm">Filters</h2>
        <button @click="filtersOpen = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto p-4">
        <form method="GET" action="{{ route('search') }}" class="space-y-5">
            <input type="hidden" name="q" value="{{ $query }}">
            <input type="hidden" name="mode" value="{{ $mode }}">

            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">File Type</h3>
                <p class="text-xs text-gray-400 mb-1.5">Documents only</p>
                @foreach (['pdf','docx','xlsx','pptx','txt','png','jpg'] as $ft)
                <label class="flex items-center gap-2 py-1 text-sm text-gray-700 cursor-pointer hover:text-blue-600">
                    <input type="radio" name="type" value="{{ $ft }}" {{ $type === $ft ? 'checked' : '' }}
                           onchange="this.form.submit()"
                           class="text-blue-600">
                    {{ strtoupper($ft) }}
                </label>
                @endforeach
                @if($type)
                <a href="{{ route('search', array_merge(request()->query(), ['type' => null])) }}"
                   class="text-xs text-blue-500 hover:underline mt-1 block">Clear type</a>
                @endif
            </div>

            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Category</h3>
                <select name="category_id" onchange="this.form.submit()"
                        class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                    <option value="">All categories</option>
                    @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Upload Date</h3>
                <div class="space-y-1.5">
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                           onchange="this.form.submit()"
                           class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs"
                           placeholder="From">
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                           onchange="this.form.submit()"
                           class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs"
                           placeholder="To">
                </div>
            </div>

            @if($type || $categoryId || $dateFrom || $dateTo)
            <a href="{{ route('search', ['q' => $query, 'mode' => $mode]) }}"
               class="block text-center text-xs text-red-500 hover:underline border border-red-200 rounded-lg py-1.5">
                Clear all filters
            </a>
            @endif
        </form>
    </div>
</div>
@endsection
