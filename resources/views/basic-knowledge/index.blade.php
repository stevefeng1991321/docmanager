@extends('layouts.app')
@section('title', 'Basic Knowledge')

@section('content')
<div class="space-y-6">

    {{-- Page header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Basic Knowledge</h1>
        <p class="text-sm text-gray-500 mt-1">Foundational concepts across science, mathematics, and more.</p>
    </div>

    {{-- Search / filter bar --}}
    <form method="GET" action="{{ route('basic-knowledge.index') }}" class="flex flex-wrap gap-2">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search entries…"
               class="w-56 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">

        <select name="category_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ (string)$category_id === (string)$cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>

        <select name="sort" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            <option value="newest"     {{ $sort === 'newest'     ? 'selected' : '' }}>Recently Added</option>
            <option value="oldest"     {{ $sort === 'oldest'     ? 'selected' : '' }}>Oldest First</option>
            <option value="title_asc"  {{ $sort === 'title_asc'  ? 'selected' : '' }}>Title A–Z</option>
            <option value="title_desc" {{ $sort === 'title_desc' ? 'selected' : '' }}>Title Z–A</option>
        </select>

        <button type="submit"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
            Search
        </button>

        @if($q || $category_id || $sort !== 'newest')
            <a href="{{ route('basic-knowledge.index') }}"
               class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
        @endif
    </form>

    {{-- Results --}}
    @if($trends->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="text-gray-400 text-sm">No entries found.</p>
            @if($q || $category_id)
                <a href="{{ route('basic-knowledge.index') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">Clear filters</a>
            @endif
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($trends as $trend)
            <a href="{{ route('basic-knowledge.show', $trend) }}"
               class="bg-white rounded-xl border border-gray-200 p-5 hover:border-blue-300 hover:shadow-sm transition group flex flex-col gap-3">
                <div class="flex items-start justify-between gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                        {{ $trend->category->name }}
                    </span>
                    <span class="text-xs text-gray-400 whitespace-nowrap">{{ $trend->created_at->format('M j, Y') }}</span>
                </div>
                <h2 class="text-base font-semibold text-gray-800 group-hover:text-blue-600 transition leading-snug line-clamp-2">
                    {{ $trend->title }}
                </h2>
                @if($trend->summary)
                    <p class="text-sm text-gray-500 line-clamp-3 leading-relaxed">{{ $trend->summary }}</p>
                @endif
                @if($trend->tags)
                    <div class="flex flex-wrap gap-1 mt-auto pt-1">
                        @foreach(array_slice($trend->tags, 0, 4) as $tag)
                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 text-xs">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($trends->hasPages())
            <div class="flex items-center justify-between text-xs text-gray-400">
                <span>Showing {{ $trends->firstItem() }}–{{ $trends->lastItem() }} of {{ $trends->total() }}</span>
                {{ $trends->links() }}
            </div>
        @endif
    @endif

</div>
@endsection
