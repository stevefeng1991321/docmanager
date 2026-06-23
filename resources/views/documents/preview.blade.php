@extends('layouts.app')
@section('title', 'Preview: ' . $resource->title)

@section('content')
<div class="space-y-3" x-data="pdfViewer()" x-init="init()">

    {{-- Toolbar --}}
    <div class="flex items-center justify-between bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-2.5 gap-3">
        <a href="{{ route('documents.show', $resource) }}" class="text-sm text-gray-600 hover:text-gray-900">
            &larr; {{ $resource->title }}
        </a>
        @if(strtolower($resource->file_type) === 'pdf')
        <div class="flex items-center gap-3 text-sm text-gray-600">
            <button @click="prevPage()" :disabled="page <= 1" class="px-2 py-1 border rounded hover:bg-gray-50 disabled:opacity-40">&#8249;</button>
            <span>Page <span x-text="page"></span> / <span x-text="totalPages"></span></span>
            <button @click="nextPage()" :disabled="page >= totalPages" class="px-2 py-1 border rounded hover:bg-gray-50 disabled:opacity-40">&#8250;</button>
            <button @click="addBookmark()" class="px-3 py-1 border border-blue-300 text-blue-600 hover:bg-blue-50 rounded text-xs font-medium">
                Bookmark Page
            </button>
        </div>
        @endif
        <a href="{{ route('documents.download', $resource) }}"
           class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">
            Download
        </a>
    </div>

    {{-- Viewer --}}
    <div class="bg-gray-800 rounded-xl overflow-hidden flex items-center justify-center" style="min-height:75vh;">
        @if(strtolower($resource->file_type) === 'pdf')
        <canvas id="pdf-canvas" class="shadow-2xl"></canvas>
        @elseif(in_array(strtolower($resource->file_type), ['png','jpg','jpeg','gif','webp']))
        <img src="{{ route('documents.download', $resource) }}" alt="{{ $resource->title }}"
             class="max-h-[75vh] object-contain shadow-2xl">
        @else
        <p class="text-gray-300">Preview not available for this file type.</p>
        @endif
    </div>

    {{-- Bookmark modal --}}
    <div x-show="showBookmarkModal" x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl p-6 w-80 space-y-4" @click.outside="showBookmarkModal = false">
            <h3 class="font-semibold text-gray-800">Bookmark Page <span x-text="page"></span></h3>
            <textarea x-model="bookmarkNote" rows="3" placeholder="Optional note…"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-none"></textarea>
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

</div>

@if(strtolower($resource->file_type) === 'pdf')
@push('scripts')
<script>
function pdfViewer() {
    return {
        page: 1,
        totalPages: 0,
        pdf: null,
        showBookmarkModal: false,
        bookmarkNote: '',

        async init() {
            // pdf.js is loaded via Flowbite bundle or can be added separately
            // For now we use the browser's built-in PDF via embed as fallback
            const canvas = document.getElementById('pdf-canvas');
            if (!canvas) return;

            // Inject an embed fallback (works in modern browsers without pdf.js)
            const container = canvas.parentElement;
            canvas.remove();
            const embed = document.createElement('embed');
            embed.src = '{{ route('documents.download', $resource) }}';
            embed.type = 'application/pdf';
            embed.style.width = '100%';
            embed.style.height = '75vh';
            container.appendChild(embed);
            this.totalPages = 1;
        },

        prevPage() { if (this.page > 1) this.page--; },
        nextPage() { if (this.page < this.totalPages) this.page++; },

        addBookmark() {
            this.bookmarkNote = '';
            this.showBookmarkModal = true;
        },

        async saveBookmark() {
            const res = await fetch('{{ route('bookmarks.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({
                    resource_id: {{ $resource->id }},
                    page_number: this.page,
                    note: this.bookmarkNote,
                }),
            });
            if (res.ok) {
                this.showBookmarkModal = false;
            }
        },
    };
}
</script>
@endpush
@endif
@endsection
