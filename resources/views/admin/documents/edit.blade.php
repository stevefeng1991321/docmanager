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
                            <option value="">— None —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected($document->category_id == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Upload New Version</h3>
            <form method="POST" action="{{ route('admin.versions.store', $document) }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="file" name="file" required class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <input type="text" name="change_note" placeholder="What changed? (optional)"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                    Upload Version
                </button>
            </form>
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
                            <span class="text-gray-400 ml-2 text-xs">{{ $version->created_at->format('d M Y H:i') }}</span>
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
