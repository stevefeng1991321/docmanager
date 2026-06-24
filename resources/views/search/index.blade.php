@extends('layouts.app')
@section('title', $query ? "Search: {$query}" : 'Search')

@section('content')
<div class="flex gap-6">

    {{-- Filter sidebar --}}
    <aside class="hidden lg:block w-52 flex-shrink-0">
        <form method="GET" action="{{ route('search') }}" id="filter-form" class="space-y-5">
            <input type="hidden" name="q" value="{{ $query }}">
            <input type="hidden" name="mode" value="{{ $mode }}">

            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">File Type</h3>
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

        {{-- Search form --}}
        <form method="GET" action="{{ route('search') }}" class="space-y-3">
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="category_id" value="{{ $categoryId }}">
            <input type="hidden" name="date_from" value="{{ $dateFrom }}">
            <input type="hidden" name="date_to" value="{{ $dateTo }}">

            <div class="flex gap-2">
                <input type="text" name="q" value="{{ $query }}" placeholder="Search documents…" autofocus
                       class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">
                    Search
                </button>
                @if($query)
                <a href="{{ route('search') }}" class="px-4 py-2.5 border border-gray-300 text-gray-500 hover:bg-gray-50 text-sm rounded-xl transition">
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

                @if($mode === 'ai')
                <span class="text-xs text-purple-600">Finds conceptually related documents</span>
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
            @else
            <span class="text-xs text-gray-400 italic">Sorted by semantic similarity</span>
            @endif
        </div>

        {{-- Results list --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">
            @forelse ($results as $doc)
            @php
                $hl = fn($text) => $query
                    ? preg_replace('/(' . preg_quote(e($query), '/') . ')/iu', '<mark class="bg-yellow-100 text-yellow-800 rounded px-0.5">$1</mark>', e($text))
                    : e($text);
                $similarity = $scores[$doc->id] ?? null;
            @endphp
            <a href="{{ route('documents.show', $doc) }}"
               class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-semibold text-gray-800 text-sm">{!! $hl($doc->title) !!}</p>
                        @if($similarity !== null)
                        <span class="text-xs font-medium text-purple-700 bg-purple-50 border border-purple-100 px-2 py-0.5 rounded-full flex-shrink-0">
                            {{ round($similarity * 100) }}% match
                        </span>
                        @endif
                    </div>
                    @if($doc->description)
                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{!! $hl($doc->description) !!}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-400 flex-wrap">
                        <span>{{ $doc->category?->name ?? 'Uncategorised' }}</span>
                        <span>{{ number_format($doc->download_count) }} downloads</span>
                        <span>{{ $doc->created_at->format('Y-m-d') }}</span>
                        @if($doc->ratings_avg_rating)
                        <span class="text-yellow-500">&#9733; {{ number_format($doc->ratings_avg_rating, 1) }}</span>
                        @endif
                    </div>
                </div>
                <span class="flex-shrink-0 text-xs text-gray-400 uppercase font-mono bg-gray-100 px-2 py-0.5 rounded">
                    {{ $doc->file_type }}
                </span>
            </a>
            @empty
            <div class="px-5 py-10 text-center text-gray-400 text-sm">
                <p class="text-3xl mb-2">&#128269;</p>
                <p>No documents match your search.</p>
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
@endsection
