@extends('layouts.admin')
@section('title', 'Upload Document')

@section('content')

<div class="max-w-2xl">
<form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-400 @enderror">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">File <span class="text-red-500">*</span></label>
            <div x-data="{ name: '' }"
                 class="border-2 border-dashed border-gray-300 hover:border-blue-400 rounded-xl p-6 text-center transition cursor-pointer"
                 @dragover.prevent @drop.prevent="name = $event.dataTransfer.files[0].name; $refs.fileInput.files = $event.dataTransfer.files">
                <input type="file" name="file" x-ref="fileInput" class="hidden" required
                       @change="name = $event.target.files[0]?.name">
                <svg class="mx-auto w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p class="text-sm text-gray-500" x-text="name || 'Drag & drop or click to browse'"></p>
                <button type="button" @click="$refs.fileInput.click()"
                        class="mt-2 text-xs text-blue-600 hover:underline">Choose file</button>
            </div>
            @error('file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">— None —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="draft"          @selected(old('status','draft')==='draft')>Draft</option>
                    <option value="pending_review" @selected(old('status')==='pending_review')>Pending Review</option>
                    <option value="published"      @selected(old('status')==='published')>Published</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $tag)
                    <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                               @checked(in_array($tag->id, old('tags', [])))
                               class="rounded border-gray-300 text-blue-600">
                        {{ $tag->name }}
                    </label>
                @endforeach
                @if($tags->isEmpty())
                    <span class="text-sm text-gray-400">No tags yet — create them in Tags section.</span>
                @endif
            </div>
        </div>

    </div>

    <div class="flex gap-3">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            Upload Document
        </button>
        <a href="{{ route('admin.documents.index') }}" class="px-6 py-2.5 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">
            Cancel
        </a>
    </div>
</form>
</div>

@endsection
