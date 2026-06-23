@extends('layouts.app')
@section('title', $query ? "Search: {$query}" : 'Search')

@section('content')
<div class="space-y-5">

    {{-- Search form --}}
    <form method="GET" action="{{ route('search') }}" class="flex gap-2">
        <input type="text" name="q" value="{{ $query }}" placeholder="Search documents…" autofocus
               class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none">
        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">
            Search
        </button>
        @if($query)
        <button type="button" onclick="document.querySelector('[name=q]').value='';this.closest('form').submit()"
                class="px-4 py-2.5 border border-gray-300 text-gray-500 hover:bg-gray-50 text-sm rounded-xl transition">
            Clear
        </button>
        @endif
    </form>

    {{-- Save search --}}
    @if($query && $total > 0)
    <form method="POST" action="{{ route('saved-searches.store') }}" class="inline">
        @csrf
        <input type="hidden" name="query" value="{{ $query }}">
        <button class="text-xs text-blue-600 hover:underline">Save this search</button>
    </form>
    @endif

    {{-- Results --}}
    @if($query)
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">
            @if($total)
            {{ number_format($total) }} result{{ $total !== 1 ? 's' : '' }} for "<strong>{{ $query }}</strong>"
            @else
            No results for "<strong>{{ $query }}</strong>"
            @endif
        </p>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">
        @forelse ($results as $doc)
        <a href="{{ route('documents.show', $doc) }}"
           class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition">
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm">{{ $doc->title }}</p>
                @if($doc->description)
                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $doc->description }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-1">
                    {{ $doc->category?->name ?? 'Uncategorised' }}
                    &middot; {{ number_format($doc->download_count) }} downloads
                    &middot; {{ $doc->created_at->format('Y-m-d') }}
                </p>
            </div>
            <span class="flex-shrink-0 text-xs text-gray-400 uppercase font-mono bg-gray-100 px-2 py-0.5 rounded">
                {{ $doc->file_type }}
            </span>
        </a>
        @empty
        <div class="px-5 py-10 text-center text-gray-400 text-sm">
            <p class="text-3xl mb-2">&#128269;</p>
            <p>No documents match your search.</p>
            <p class="mt-1">Try different keywords or browse by category.</p>
        </div>
        @endforelse
    </div>

    @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div>{{ $results->links() }}</div>
    @endif

    @else
    {{-- Suggestions / saved searches --}}
    @if($savedSearches->count())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <h3 class="text-xs font-semibold uppercase text-gray-500 mb-2">Saved Searches</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($savedSearches as $saved)
            <a href="{{ route('search', ['q' => $saved->query]) }}"
               class="px-3 py-1 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 rounded-full text-sm transition">
                {{ $saved->query }}
            </a>
            @endforeach
        </div>
    </div>
    @endif
    @endif

</div>
@endsection
