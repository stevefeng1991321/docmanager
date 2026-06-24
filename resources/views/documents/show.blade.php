@extends('layouts.app')
@section('title', $resource->title)

@section('content')
<div class="flex gap-6">

    {{-- Main --}}
    <div class="flex-1 min-w-0 space-y-5">

        {{-- Back --}}
        <a href="javascript:history.back()"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>

        {{-- Header --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <h1 class="text-xl font-bold text-gray-900 leading-tight">{{ $resource->title }}</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $resource->category?->name ?? 'Uncategorised' }}
                        &middot; {{ number_format($resource->file_size / 1024) }} KB
                        &middot; {{ strtoupper($resource->file_type) }}
                        &middot; v{{ $resource->current_version }}
                    </p>
                    @if($resource->tags->count())
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        @foreach($resource->tags as $tag)
                        <a href="{{ route('tags.show', $tag) }}"
                           class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded text-xs transition">
                            {{ $tag->name }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    {{-- Favorite --}}
                    @if($isFavorited)
                    <form method="POST" action="{{ route('favorites.destroy', $resource) }}">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1.5 border border-yellow-300 bg-yellow-50 text-yellow-700 text-xs font-medium rounded-lg hover:bg-yellow-100 transition">
                            &#9733; Saved
                        </button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('favorites.store', $resource) }}">
                        @csrf
                        <button class="px-3 py-1.5 border border-gray-200 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-50 transition">
                            &#9733; Save
                        </button>
                    </form>
                    @endif

                    {{-- Download (blocked when locked) --}}
                    @if($resource->isLocked())
                    <span class="px-4 py-1.5 bg-gray-200 text-gray-400 text-xs font-semibold rounded-lg cursor-not-allowed flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Locked
                    </span>
                    @else
                    <a href="{{ route('documents.download', $resource) }}"
                       class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">
                        Download
                    </a>
                    @endif
                </div>
            </div>

            @if($resource->description)
            <p class="mt-4 text-sm text-gray-600 leading-relaxed">{{ $resource->description }}</p>
            @endif
        </div>

        {{-- Locked notice --}}
        @if($resource->isLocked())
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 flex items-center gap-3">
            <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <div class="text-sm text-yellow-800">
                <strong>Document locked</strong> by {{ $resource->locker?->name ?? 'an admin' }}.
                Downloading and previewing are disabled until it is unlocked.
            </div>
        </div>
        @endif

        {{-- PDF Preview --}}
        @php $ft = strtolower($resource->file_type ?? ''); @endphp
        @if(!$resource->isLocked() && ($ft === 'application/pdf' || str_starts_with($ft, 'image/')))
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <a href="{{ route('documents.preview', $resource) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 border border-blue-300 text-blue-600 hover:bg-blue-50 text-sm font-medium rounded-lg transition">
                Open Preview
            </a>
        </div>
        @endif

        {{-- Rating --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5"
             x-data="{ score: {{ $userRating?->rating ?? 0 }} }">
            <h3 class="font-semibold text-gray-800 mb-3">Rate this Document</h3>
            <form method="POST" action="{{ route('ratings.store', $resource) }}" class="space-y-3">
                @csrf
                <div class="flex gap-1">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" @click="score = {{ $i }}"
                            :class="score >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                            class="text-2xl leading-none hover:text-yellow-400 transition focus:outline-none">&#9733;</button>
                    @endfor
                    <input type="hidden" name="score" :value="score">
                </div>
                <textarea name="review" rows="2" placeholder="Optional review…"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none">{{ $userRating?->review }}</textarea>
                <button type="submit" :disabled="score === 0"
                        class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-40 text-white text-sm font-medium rounded-lg transition">
                    Submit Rating
                </button>
            </form>
            @if($resource->ratings->count())
            <p class="mt-3 text-xs text-gray-400">
                Average: &#9733; {{ number_format($resource->averageRating(), 1) }} from {{ $resource->ratings->count() }} rating(s)
            </p>
            @endif
        </div>

        {{-- Add to reading list --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Add to Reading List</h3>
            @if(auth()->user()->readingLists()->count())
            <form method="POST" class="flex gap-2" id="add-to-list-form">
                @csrf
                <select name="list_id" id="list_id"
                        class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                    @foreach(auth()->user()->readingLists as $list)
                    <option value="{{ $list->id }}">{{ $list->name }}</option>
                    @endforeach
                </select>
                <button type="submit" id="add-to-list-btn"
                        class="px-4 py-1.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
                    Add
                </button>
            </form>
            <script>
            document.getElementById('add-to-list-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const listId = document.getElementById('list_id').value;
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/reading-lists/' + listId + '/items/{{ $resource->id }}';
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = document.querySelector('meta[name="csrf-token"]').content;
                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
            });
            </script>
            @else
            <p class="text-sm text-gray-400">
                No reading lists yet. <a href="{{ route('reading-lists.index') }}" class="text-blue-600 hover:underline">Create one</a>.
            </p>
            @endif
        </div>

    </div>

    {{-- Sidebar --}}
    <aside class="hidden lg:block w-64 flex-shrink-0 space-y-4">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-sm space-y-2">
            <h3 class="font-semibold text-gray-700 mb-2">Details</h3>
            <div class="flex justify-between text-gray-600">
                <span class="text-gray-400">Version</span>
                <span>v{{ $resource->current_version }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span class="text-gray-400">Downloads</span>
                <span>{{ number_format($resource->download_count) }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span class="text-gray-400">File size</span>
                <span>{{ number_format($resource->file_size / 1024) }} KB</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span class="text-gray-400">Added</span>
                <span>{{ $resource->created_at->format('Y-m-d') }}</span>
            </div>
        </div>

        {{-- Share link --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <h3 class="font-semibold text-gray-700 text-sm mb-2">Share</h3>
            @if(session('share_url'))
            <div class="bg-gray-50 rounded-lg p-2 text-xs text-gray-600 break-all select-all">{{ session('share_url') }}</div>
            @else
            <form method="POST" action="{{ route('documents.share', $resource) }}">
                @csrf
                <button class="w-full px-3 py-1.5 border border-gray-200 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-50 transition">
                    Generate Share Link
                </button>
            </form>
            @endif
        </div>

        {{-- Version history --}}
        @if($resource->versions->count() > 1)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <h3 class="font-semibold text-gray-700 text-sm mb-2">Versions</h3>
            <ul class="space-y-1">
                @foreach($resource->versions->sortByDesc('version_number') as $ver)
                <li class="flex justify-between text-xs text-gray-500">
                    <span>v{{ $ver->version_number }}</span>
                    <span>{{ $ver->created_at?->format('Y-m-d') ?? '—' }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Related documents --}}
        @if($related->count())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <h3 class="font-semibold text-gray-700 text-sm mb-3">Related Documents</h3>
            <ul class="space-y-2">
                @foreach($related as $rel)
                <li>
                    <a href="{{ route('documents.show', $rel) }}" class="block hover:text-blue-600 transition">
                        <p class="text-xs font-medium text-gray-700 leading-snug">{{ Str::limit($rel->title, 50) }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ number_format($rel->download_count) }} dl
                            @if($rel->ratings_avg_rating)
                            &middot; <span class="text-yellow-500">&#9733; {{ number_format($rel->ratings_avg_rating, 1) }}</span>
                            @endif
                        </p>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

    </aside>
</div>
@endsection
