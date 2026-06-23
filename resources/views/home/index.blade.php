@extends('layouts.app')
@section('title', 'Technical Library')

@section('content')
<div class="flex gap-6">

    {{-- Sidebar: categories --}}
    <aside class="hidden lg:block w-52 flex-shrink-0">
        <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Browse by Category</h2>
        <nav class="space-y-1">
            <a href="{{ route('home') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('home') && !request('category') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                All Documents
            </a>
            @foreach ($categories as $cat)
            <a href="{{ route('categories.show', $cat) }}"
               class="flex items-center justify-between px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100">
                <span>{{ $cat->name }}</span>
                <span class="text-xs text-gray-400">{{ $cat->resources_count }}</span>
            </a>
            @endforeach
        </nav>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 min-w-0 space-y-8">

        {{-- Featured / most downloaded --}}
        @if($featured->count())
        <section>
            <h2 class="text-base font-semibold text-gray-800 mb-3">Most Downloaded</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($featured as $doc)
                <a href="{{ route('documents.show', $doc) }}"
                   class="block bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md hover:border-blue-200 transition p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 text-sm truncate">{{ $doc->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $doc->category?->name ?? 'Uncategorised' }}</p>
                        </div>
                        <span class="flex-shrink-0 px-2 py-0.5 bg-gray-100 text-gray-500 text-xs rounded font-mono uppercase">
                            {{ $doc->file_type }}
                        </span>
                    </div>
                    @if($doc->description)
                    <p class="text-xs text-gray-400 mt-2 line-clamp-2">{{ $doc->description }}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
                        <span>{{ number_format($doc->download_count) }} downloads</span>
                        @if($doc->averageRating())
                        <span>&#9733; {{ number_format($doc->averageRating(), 1) }}</span>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Recently added --}}
        <section>
            <h2 class="text-base font-semibold text-gray-800 mb-3">Recently Added</h2>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">
                @forelse ($recent as $doc)
                <a href="{{ route('documents.show', $doc) }}"
                   class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 transition">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 text-sm truncate">{{ $doc->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $doc->category?->name ?? 'Uncategorised' }} &middot; {{ $doc->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="flex-shrink-0 text-xs text-gray-400 uppercase font-mono">{{ $doc->file_type }}</span>
                </a>
                @empty
                <p class="px-5 py-8 text-center text-gray-400 text-sm">No documents published yet.</p>
                @endforelse
            </div>
        </section>

    </div>
</div>
@endsection
