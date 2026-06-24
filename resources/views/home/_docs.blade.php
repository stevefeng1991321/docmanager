{{-- Partial: rendered by HomeController@browse for both initial load and AJAX --}}

@if($view === 'grid')
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse ($allDocs as $doc)
    <a href="{{ route('documents.show', $doc) }}"
       class="block bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md hover:border-blue-200 transition p-4">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <p class="font-semibold text-gray-800 text-sm truncate">{{ $doc->title }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $doc->category?->name ?? 'Uncategorised' }}</p>
            </div>
            <span class="flex-shrink-0 px-2 py-0.5 bg-gray-100 text-gray-500 text-xs rounded font-mono uppercase">
                {{ $doc->file_type }}
            </span>
        </div>
        @if($doc->description)
        <p class="text-xs text-gray-400 mt-2 line-clamp-2">{{ $doc->description }}</p>
        @endif
        <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
            <span>{{ $doc->created_at->format('Y-m-d') }}</span>
            @if($doc->ratings_avg_rating)
            <span class="text-yellow-500">&#9733; {{ number_format($doc->ratings_avg_rating, 1) }}</span>
            @endif
        </div>
    </a>
    @empty
    <p class="col-span-full py-10 text-center text-gray-400 text-sm">No documents found.</p>
    @endforelse
</div>
@else
<div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">
    @forelse ($allDocs as $doc)
    <a href="{{ route('documents.show', $doc) }}"
       class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 transition">
        <div class="flex-1 min-w-0">
            <p class="font-medium text-gray-800 text-sm truncate">{{ $doc->title }}</p>
            <p class="text-xs text-gray-400 mt-0.5">
                {{ $doc->category?->name ?? 'Uncategorised' }}
                &middot; {{ $doc->created_at->diffForHumans() }}
                @if($doc->ratings_avg_rating)
                &middot; <span class="text-yellow-500">&#9733; {{ number_format($doc->ratings_avg_rating, 1) }}</span>
                @endif
            </p>
        </div>
        <span class="flex-shrink-0 text-xs text-gray-400 uppercase font-mono">{{ $doc->file_type }}</span>
    </a>
    @empty
    <p class="px-5 py-10 text-center text-gray-400 text-sm">No documents found.</p>
    @endforelse
</div>
@endif

<div class="flex items-center justify-between gap-3 mt-4 flex-wrap">
    @if($allDocs->total() > 10)
    <select
        onchange="document.dispatchEvent(new CustomEvent('per-page-change', { detail: { perPage: parseInt(this.value) } }))"
        class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
    >
        @foreach([10, 20, 30] as $size)
            <option value="{{ $size }}" @selected(($perPage ?? 20) == $size)>{{ $size }} / page</option>
        @endforeach
    </select>
    @endif
    <div data-pagination>
        {{ $allDocs->links('vendor.pagination.admin-compact') }}
    </div>
</div>
