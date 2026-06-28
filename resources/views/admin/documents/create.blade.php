@extends('layouts.admin')
@section('title', __('documents.upload_heading'))

@section('content')

<div class="max-w-2xl" x-data="chunkedUpload()" x-init="init()">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">

        {{-- Title --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('documents.title_label') }} <span class="text-red-500">*</span></label>
            <input type="text" x-model="title" placeholder="{{ __('documents.title_placeholder') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                   :class="{'border-red-400': errors.title}">
            <p x-show="errors.title" x-text="errors.title" class="mt-1 text-xs text-red-600"></p>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.description') }}</label>
            <textarea x-model="description" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>

        {{-- Storage quota --}}
        @if($quota['quota_bytes'] !== null)
        <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 space-y-1.5">
            <div class="flex justify-between text-xs text-gray-500">
                <span>Storage quota</span>
                <span>{{ $quota['used_human'] }} / {{ $quota['quota_human'] }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                <div class="h-1.5 rounded-full transition-all"
                     style="width: {{ $quota['percent'] }}%"
                     :class="{
                         'bg-green-500': {{ $quota['percent'] }} < 75,
                         'bg-yellow-400': {{ $quota['percent'] }} >= 75 && {{ $quota['percent'] }} < 90,
                         'bg-red-500': {{ $quota['percent'] }} >= 90
                     }"></div>
            </div>
            <p class="text-xs text-gray-400">
                {{ number_format($quota['remaining_bytes'] / 1024 / 1024, 1) }} MB remaining
            </p>
        </div>
        @endif

        {{-- File picker --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('documents.file_label') }} <span class="text-red-500">*</span></label>
            <div class="border-2 border-dashed rounded-xl p-6 text-center transition cursor-pointer"
                 :class="file ? 'border-blue-400 bg-blue-50' : 'border-gray-300 hover:border-blue-400'"
                 @dragover.prevent
                 @drop.prevent="pickFile($event.dataTransfer.files[0])"
                 @click="$refs.fileInput.click()">
                <input type="file" x-ref="fileInput" class="hidden"
                       @change="pickFile($event.target.files[0])">
                <svg class="mx-auto w-10 h-10 mb-2 transition"
                     :class="file ? 'text-blue-400' : 'text-gray-300'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p class="text-sm font-medium transition"
                   :class="file ? 'text-blue-700' : 'text-gray-500'"
                   x-text="file ? file.name + ' (' + fmtBytes(file.size) + ')' : 'Drag & drop or click to browse'"></p>
                <p x-show="!file" class="mt-1 text-xs text-gray-400">
                    Max {{ config('app.max_upload_size_mb', 50) }} MB — files over 5 MB upload in chunks automatically
                </p>
            </div>
            <p x-show="errors.file" x-text="errors.file" class="mt-1 text-xs text-red-600"></p>
        </div>

        {{-- Progress bar (visible while uploading) --}}
        <div x-show="uploading" x-cloak class="space-y-1.5">
            <div class="flex justify-between text-xs text-gray-500">
                <span x-text="statusMsg"></span>
                <span x-text="progress + '%'"></span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                <div class="bg-blue-500 h-2.5 rounded-full transition-all duration-200"
                     :style="'width:' + progress + '%'"></div>
            </div>
        </div>

        {{-- Category + Status --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.category') }}</label>
                <select x-model="categoryId" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @include('admin.partials.category_options')
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.status') }}</label>
                <select x-model="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="draft">{{ __('common.status_draft') }}</option>
                    <option value="pending_review">{{ __('common.status_pending_review') }}</option>
                    <option value="published">{{ __('common.status_published') }}</option>
                </select>
            </div>
        </div>

        {{-- Tags --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.tags') }}</label>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $tag)
                    <label class="flex items-center gap-1.5 text-sm cursor-pointer select-none">
                        <input type="checkbox" value="{{ $tag->id }}" @change="toggleTag('{{ $tag->id }}')"
                               class="rounded border-gray-300 text-blue-600">
                        {{ $tag->name }}
                    </label>
                @endforeach
                @if($tags->isEmpty())
                    <span class="text-sm text-gray-400">No tags yet — create them in the Tags section.</span>
                @endif
            </div>
        </div>

    </div>

    {{-- Actions --}}
    <div class="flex gap-3 mt-5">
        <button type="button" @click="submit()" :disabled="uploading"
                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg transition"
                x-text="uploading ? 'Uploading…' : 'Upload Document'">
        </button>
        <a href="{{ route('admin.documents.index') }}"
           class="px-6 py-2.5 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">
            {{ __('common.cancel') }}
        </a>
    </div>

</div>

<script>
function chunkedUpload() {
    return {
        title: '',
        description: '',
        categoryId: '',
        status: 'draft',
        tags: [],
        file: null,
        uploading: false,
        progress: 0,
        statusMsg: '',
        errors: {},

        CHUNK_SIZE: 2 * 1024 * 1024,
        remainingQuota: {{ $quota['remaining_bytes'] ?? 'null' }},

        init() {},

        pickFile(f) {
            if (!f) return;
            this.file = f;
            this.errors = {};
            if (!this.title.trim()) {
                this.title = f.name.replace(/\.[^/.]+$/, '').replace(/[_-]+/g, ' ').trim();
            }
        },

        toggleTag(id) {
            const i = this.tags.indexOf(id);
            i === -1 ? this.tags.push(id) : this.tags.splice(i, 1);
        },

        fmtBytes(b) {
            if (b < 1024)     return b + ' B';
            if (b < 1048576)  return (b / 1024).toFixed(1) + ' KB';
            return (b / 1048576).toFixed(1) + ' MB';
        },

        validate() {
            this.errors = {};
            if (!this.title.trim()) this.errors.title = 'Title is required.';
            if (!this.file)         this.errors.file  = 'Please select a file.';
            if (this.file && this.remainingQuota !== null && this.file.size > this.remainingQuota) {
                this.errors.file = `File is too large (${this.fmtBytes(this.file.size)}). You only have ${this.fmtBytes(this.remainingQuota)} remaining in your quota.`;
            }
            return Object.keys(this.errors).length === 0;
        },

        uid() {
            const arr = new Uint8Array(16);
            crypto.getRandomValues(arr);
            return Array.from(arr, b => b.toString(16).padStart(2, '0')).join('');
        },

        async submit() {
            if (!this.validate()) return;

            this.uploading = true;
            this.progress  = 0;
            this.errors    = {};

            const fileId      = this.uid();
            const totalChunks = Math.ceil(this.file.size / this.CHUNK_SIZE);
            const csrf        = document.querySelector('meta[name="csrf-token"]').content;

            // ── 1. Upload chunks ──────────────────────────────────────────
            for (let i = 0; i < totalChunks; i++) {
                const blob = this.file.slice(i * this.CHUNK_SIZE, (i + 1) * this.CHUNK_SIZE);
                const fd   = new FormData();
                fd.append('_token',       csrf);
                fd.append('file_id',      fileId);
                fd.append('chunk_index',  i);
                fd.append('total_chunks', totalChunks);
                fd.append('file_size',    this.file.size);
                fd.append('chunk',        blob, this.file.name);

                this.statusMsg = `Uploading part ${i + 1} of ${totalChunks}…`;

                const res = await fetch('{{ route("admin.documents.upload.chunk") }}', {
                    method: 'POST', body: fd,
                });

                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    this.errors.file = err.message || `Upload failed at part ${i + 1}.`;
                    this.uploading   = false;
                    return;
                }

                this.progress = Math.round(((i + 1) / totalChunks) * 85);
            }

            // ── 2. Assemble ───────────────────────────────────────────────
            this.statusMsg = 'Assembling file…';
            this.progress  = 90;

            const afd = new FormData();
            afd.append('_token',        csrf);
            afd.append('file_id',       fileId);
            afd.append('total_chunks',  totalChunks);
            afd.append('original_name', this.file.name);
            afd.append('title',         this.title);
            afd.append('description',   this.description);
            afd.append('category_id',   this.categoryId);
            afd.append('status',        this.status);
            this.tags.forEach(t => afd.append('tags[]', t));

            const ares = await fetch('{{ route("admin.documents.upload.assemble") }}', {
                method: 'POST', body: afd,
            });

            const data = await ares.json().catch(() => ({}));

            if (!ares.ok) {
                this.errors.file = data.error || 'Assembly failed. Please try again.';
                this.uploading   = false;
                return;
            }

            this.progress  = 100;
            this.statusMsg = 'Upload complete! Redirecting…';
            window.location.href = data.redirect;
        },
    };
}
</script>

@endsection
