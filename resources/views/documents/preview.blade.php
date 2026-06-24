@extends('layouts.app')
@section('title', 'Preview: ' . $resource->title)

@section('content')
@php $ft = strtolower($resource->file_type ?? ''); @endphp
<div class="space-y-3" x-data="{ showBookmarkModal: false, bookmarkNote: '' }">

    {{-- Toolbar --}}
    <div class="flex items-center justify-between bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-2.5 gap-3 flex-wrap">
        <a href="{{ route('documents.show', $resource) }}" class="text-sm text-gray-600 hover:text-gray-900 truncate min-w-0">
            &larr; {{ $resource->title }}
        </a>
        <div class="flex items-center gap-2 flex-shrink-0">
            @if($ft === 'application/pdf')
            <button @click="showBookmarkModal = true"
                    class="px-3 py-1.5 border border-blue-300 text-blue-600 hover:bg-blue-50 rounded-lg text-xs font-medium transition">
                Bookmark Page
            </button>
            @endif
            <a href="{{ route('documents.download', $resource) }}"
               class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">
                Download
            </a>
        </div>
    </div>

    {{-- Viewer --}}
    @if($ft === 'application/pdf')
    <div x-data="pdfViewer('{{ route('documents.stream', $resource) }}')" x-init="init()">

        {{-- PDF toolbar --}}
        <div class="flex items-center justify-between bg-gray-700 rounded-t-xl px-4 py-2 gap-3 flex-wrap text-white text-sm">
            <div class="flex items-center gap-2">
                <button @click="prev()" :disabled="currentPage <= 1"
                        class="px-2 py-1 rounded bg-gray-600 hover:bg-gray-500 disabled:opacity-40 disabled:cursor-not-allowed transition text-xs font-medium">
                    &lsaquo; Prev
                </button>
                <span class="text-gray-300 text-xs">
                    Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                </span>
                <button @click="next()" :disabled="currentPage >= totalPages"
                        class="px-2 py-1 rounded bg-gray-600 hover:bg-gray-500 disabled:opacity-40 disabled:cursor-not-allowed transition text-xs font-medium">
                    Next &rsaquo;
                </button>
            </div>
            <div class="flex items-center gap-2">
                <button @click="zoomOut()" class="px-2 py-1 rounded bg-gray-600 hover:bg-gray-500 transition text-xs font-medium">&#8722;</button>
                <span class="text-gray-300 text-xs w-12 text-center" x-text="scalePercent()"></span>
                <button @click="zoomIn()"  class="px-2 py-1 rounded bg-gray-600 hover:bg-gray-500 transition text-xs font-medium">&#43;</button>
            </div>
        </div>

        {{-- Canvas --}}
        <div class="bg-gray-800 rounded-b-xl overflow-auto" style="min-height: 75vh; max-height: 82vh;">
            <div x-show="loading" class="flex items-center justify-center text-gray-400" style="min-height: 75vh;">
                <svg class="animate-spin w-8 h-8" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
            </div>
            <div x-show="error" x-cloak class="flex items-center justify-center text-red-400 p-8" style="min-height: 75vh;">
                <p x-text="error"></p>
            </div>
            <div x-show="!loading && !error" class="flex justify-center p-4">
                <canvas x-ref="canvas" class="shadow-2xl"></canvas>
            </div>
        </div>
    </div>

    @elseif(str_starts_with($ft, 'image/'))
    <div class="bg-gray-800 rounded-xl flex items-center justify-center p-4" style="min-height: 75vh;">
        <img src="{{ route('documents.stream', $resource) }}"
             alt="{{ $resource->title }}"
             class="max-w-full max-h-screen object-contain shadow-2xl rounded">
    </div>
    @else
    <div class="bg-gray-800 rounded-xl flex items-center justify-center text-gray-300" style="min-height: 75vh;">
        <p>Preview not available for this file type.</p>
    </div>
    @endif

    {{-- Bookmark modal --}}
    @if($ft === 'application/pdf')
    <div x-show="showBookmarkModal" x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm space-y-4" @click.outside="showBookmarkModal = false">
            <h3 class="font-semibold text-gray-800">Add Bookmark</h3>
            <textarea x-model="bookmarkNote" rows="3" placeholder="Optional note…"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-300"></textarea>
            <div class="flex gap-2 justify-end">
                <button @click="showBookmarkModal = false"
                        class="px-4 py-1.5 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button @click="saveBookmark()"
                        class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    Save
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function saveBookmark() {
        // Read current page from pdfViewer Alpine component if present
        const viewer   = document.querySelector('[x-data^="pdfViewer"]');
        const pageNum  = viewer?._x_dataStack?.[0]?.currentPage ?? 1;
        const noteEl   = document.querySelector('[x-model="bookmarkNote"]');
        const note     = noteEl ? Alpine.$data(noteEl).bookmarkNote : '';

        fetch('{{ route('bookmarks.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            },
            body: JSON.stringify({
                resource_id: {{ $resource->id }},
                page_number: pageNum,
                label: note,
            }),
        }).then(r => r.ok && location.reload());
    }
    </script>
    @endpush
    @endif

</div>
@endsection
