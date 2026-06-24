@extends('layouts.admin')
@section('title', 'Edit Document')

@section('content')

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Main form --}}
    <div class="xl:col-span-2 space-y-5">

        <form method="POST" action="{{ route('admin.documents.update', $document) }}" class="space-y-5">
            @csrf @method('PUT')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title', $document->title) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description', $document->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            @include('admin.partials.category_options', ['selected' => $document->category_id])
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
                            {{ ucfirst(str_replace('_',' ', $document->status)) }}
                            <span class="text-xs text-gray-400 ml-1">(change via Approve/Reject)</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tags as $tag)
                            <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                       @checked($document->tags->contains($tag->id))
                                       class="rounded border-gray-300 text-blue-600">
                                {{ $tag->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                Save Changes
            </button>
        </form>

        {{-- Upload new version --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6"
             x-data="versionUpload('{{ route('admin.documents.upload.version', $document) }}')"
             x-init="init()">
            <h3 class="font-semibold text-gray-800 mb-4">Upload New Version</h3>
            <div class="space-y-3">
                <div>
                    <div class="border-2 border-dashed rounded-xl p-4 text-center transition cursor-pointer"
                         :class="file ? 'border-blue-400 bg-blue-50' : 'border-gray-300 hover:border-blue-400'"
                         @dragover.prevent
                         @drop.prevent="pickFile($event.dataTransfer.files[0])"
                         @click="$refs.versionFileInput.click()">
                        <input type="file" x-ref="versionFileInput" class="hidden"
                               @change="pickFile($event.target.files[0])">
                        <p class="text-sm transition"
                           :class="file ? 'text-blue-700 font-medium' : 'text-gray-500'"
                           x-text="file ? file.name + ' (' + fmtBytes(file.size) + ')' : 'Drag & drop or click to browse'"></p>
                        <p x-show="!file" class="mt-1 text-xs text-gray-400">
                            Max {{ config('app.max_upload_size_mb', 50) }} MB — large files upload in chunks
                        </p>
                    </div>
                    <p x-show="error" x-text="error" x-cloak class="mt-1 text-xs text-red-600"></p>
                </div>

                <div x-show="uploading" x-cloak class="space-y-1">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span x-text="statusMsg"></span>
                        <span x-text="progress + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-200"
                             :style="'width:' + progress + '%'"></div>
                    </div>
                </div>

                <input type="text" x-model="changeNote" placeholder="What changed? (optional)"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">

                <button type="button" @click="submit()" :disabled="uploading || !file"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition"
                        x-text="uploading ? 'Uploading…' : 'Upload Version'">
                </button>
            </div>
        </div>

        {{-- Approve / Reject --}}
        @if($document->status === 'pending_review')
            <div class="bg-white rounded-xl shadow-sm border border-yellow-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Review Decision</h3>
                <div class="flex gap-3">
                    <form action="{{ route('admin.documents.approve', $document) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition">
                            Approve & Publish
                        </button>
                    </form>
                    <form action="{{ route('admin.documents.reject', $document) }}" method="POST" x-data="{ reason: '' }">
                        @csrf @method('PATCH')
                        <div class="flex gap-2">
                            <input type="text" name="reason" x-model="reason" placeholder="Rejection reason…"
                                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64">
                            <button class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                                Reject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>

    {{-- Sidebar: file info + version history --}}
    <div class="space-y-5">

        {{-- File details --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-3">File Info</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Filename</dt>
                    <dd class="text-gray-900 font-medium truncate max-w-[180px]">{{ $document->original_filename }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Size</dt>
                    <dd class="text-gray-900">{{ number_format($document->file_size / 1024, 1) }} KB</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Downloads</dt>
                    <dd class="text-gray-900">{{ number_format($document->download_count) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">SHA256</dt>
                    <dd class="text-gray-400 text-xs font-mono truncate max-w-[180px]" title="{{ $document->file_hash }}">
                        {{ substr($document->file_hash, 0, 12) }}…
                    </dd>
                </div>
                @if($document->locked_by)
                    <div class="pt-2 border-t border-gray-100">
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-medium">
                            Locked by {{ $document->locker?->name ?? 'Unknown' }}
                        </span>
                        @if(auth()->user()->isAdmin())
                            <form action="{{ route('admin.documents.unlock', $document) }}" method="POST" class="mt-2">
                                @csrf @method('PATCH')
                                <button class="text-xs text-gray-500 hover:text-red-600 underline">Force unlock</button>
                            </form>
                        @endif
                    </div>
                @endif
            </dl>
        </div>

        {{-- Version history --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Version History</h3>
            <div class="space-y-2">
                @foreach($document->versions as $version)
                    <div class="flex items-start justify-between gap-2 text-sm py-2 border-b border-gray-50 last:border-0">
                        <div>
                            <span class="font-medium text-gray-800">v{{ $version->version_number }}</span>
                            <span class="text-gray-400 ml-2 text-xs">{{ $version->created_at?->format('d M Y H:i') ?? '—' }}</span>
                            @if($version->change_note)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $version->change_note }}</p>
                            @endif
                            <p class="text-xs text-gray-400">by {{ $version->uploader?->name }}</p>
                        </div>
                        @if($version->version_number !== $document->versions->first()->version_number)
                            <form action="{{ route('admin.versions.restore', [$document, $version]) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="text-xs text-blue-600 hover:underline flex-shrink-0">Restore</button>
                            </form>
                        @else
                            <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">Current</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Access log --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Access Log</h3>
            <p class="text-xs text-gray-400 mb-3">Who viewed, previewed, or downloaded this document.</p>
            <a href="{{ route('admin.documents.access-log', $document) }}"
               class="block w-full text-center px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-sm font-medium text-gray-700 rounded-lg transition">
                View Access Log
            </a>
        </div>

        {{-- Danger zone --}}
        <div class="bg-white rounded-xl shadow-sm border border-red-100 p-5">
            <h3 class="font-semibold text-red-700 mb-3">Danger Zone</h3>
            <form action="{{ route('admin.documents.destroy', $document) }}" method="POST"
                  onsubmit="return confirm('Move this document to Trash?')">
                @csrf @method('DELETE')
                <button class="w-full px-4 py-2 border border-red-300 text-red-600 hover:bg-red-50 text-sm font-medium rounded-lg transition">
                    Move to Trash
                </button>
            </form>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function versionUpload(assembleUrl) {
    return {
        file: null,
        uploading: false,
        progress: 0,
        statusMsg: '',
        error: '',
        changeNote: '',

        CHUNK_SIZE: 2 * 1024 * 1024,

        init() {},

        pickFile(f) {
            if (!f) return;
            this.file  = f;
            this.error = '';
        },

        fmtBytes(b) {
            if (b < 1024)    return b + ' B';
            if (b < 1048576) return (b / 1024).toFixed(1) + ' KB';
            return (b / 1048576).toFixed(1) + ' MB';
        },

        uid() {
            const arr = new Uint8Array(16);
            crypto.getRandomValues(arr);
            return Array.from(arr, b => b.toString(16).padStart(2, '0')).join('');
        },

        async submit() {
            if (!this.file) return;

            this.uploading = true;
            this.progress  = 0;
            this.error     = '';

            const fileId      = this.uid();
            const totalChunks = Math.ceil(this.file.size / this.CHUNK_SIZE);
            const csrf        = document.querySelector('meta[name="csrf-token"]').content;
            const chunkUrl    = '{{ route("admin.documents.upload.chunk") }}';

            for (let i = 0; i < totalChunks; i++) {
                const blob = this.file.slice(i * this.CHUNK_SIZE, (i + 1) * this.CHUNK_SIZE);
                const fd   = new FormData();
                fd.append('_token',       csrf);
                fd.append('file_id',      fileId);
                fd.append('chunk_index',  i);
                fd.append('total_chunks', totalChunks);
                fd.append('chunk',        blob, this.file.name);

                this.statusMsg = `Uploading part ${i + 1} of ${totalChunks}…`;

                const res = await fetch(chunkUrl, { method: 'POST', body: fd });
                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    this.error     = err.message || `Upload failed at part ${i + 1}.`;
                    this.uploading = false;
                    return;
                }

                this.progress = Math.round(((i + 1) / totalChunks) * 85);
            }

            this.statusMsg = 'Assembling…';
            this.progress  = 90;

            const afd = new FormData();
            afd.append('_token',        csrf);
            afd.append('file_id',       fileId);
            afd.append('total_chunks',  totalChunks);
            afd.append('original_name', this.file.name);
            afd.append('change_note',   this.changeNote);

            const ares = await fetch(assembleUrl, { method: 'POST', body: afd });
            const data = await ares.json().catch(() => ({}));

            if (!ares.ok) {
                this.error     = data.error || 'Assembly failed. Please try again.';
                this.uploading = false;
                return;
            }

            this.progress  = 100;
            this.statusMsg = 'Complete! Redirecting…';
            window.location.href = data.redirect;
        },
    };
}
</script>
@endpush
