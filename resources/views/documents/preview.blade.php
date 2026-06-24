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
    <div class="bg-gray-800 rounded-xl overflow-hidden" style="min-height: 75vh;">
        @if($ft === 'application/pdf')
        <iframe src="{{ route('documents.stream', $resource) }}"
                class="w-full block"
                style="height: 80vh; border: none;"
                title="{{ $resource->title }}">
        </iframe>
        @elseif(str_starts_with($ft, 'image/'))
        <div class="flex items-center justify-center p-4" style="min-height: 75vh;">
            <img src="{{ route('documents.stream', $resource) }}"
                 alt="{{ $resource->title }}"
                 class="max-w-full max-h-screen object-contain shadow-2xl rounded">
        </div>
        @else
        <div class="flex items-center justify-center text-gray-300" style="min-height: 75vh;">
            <p>Preview not available for this file type.</p>
        </div>
        @endif
    </div>

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
        const note = document.querySelector('[x-model="bookmarkNote"]')?.value ?? '';
        fetch('{{ route('bookmarks.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            },
            body: JSON.stringify({
                resource_id: {{ $resource->id }},
                page_number: 1,
                note: note,
            }),
        }).then(() => { location.reload(); });
    }
    </script>
    @endpush
    @endif

</div>
@endsection
