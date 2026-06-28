@extends('layouts.admin')
@section('title', 'Edit: ' . $trend->title)

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">{{ __('admin.basic_knowledge.edit') }}</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.basic-knowledge.show', $trend) }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('common.view') }}</a>
            <a href="{{ route('admin.basic-knowledge.index') }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('common.back') }}</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.basic-knowledge.update', $trend) }}"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.category') }}</label>
                <select name="category_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('category_id') border-red-400 @enderror">
                    <option value="">Select category…</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $trend->category_id) == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.status') }}</label>
                <select name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('status') border-red-400 @enderror">
                    @foreach(['draft', 'published', 'archived'] as $s)
                        <option value="{{ $s }}" @selected(old('status', $trend->status) === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.title') }}</label>
            <input type="text" name="title" value="{{ old('title', $trend->title) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('title') border-red-400 @enderror">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.tags') }} <span class="text-gray-400 font-normal">{{ __('common.comma_separated') }}</span></label>
            <input type="text" name="tags"
                   value="{{ old('tags', $trend->tags ? implode(', ', $trend->tags) : '') }}"
                   placeholder="e.g. fundamentals, beginner, mathematics"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('tags') border-red-400 @enderror">
            @error('tags') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.summary') }}</label>
            <textarea name="summary" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y @error('summary') border-red-400 @enderror"
                      placeholder="{{ __('common.summary_placeholder') }}">{{ old('summary', $trend->summary) }}</textarea>
            @error('summary') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.content') }}</label>
            <textarea name="content" rows="14"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y @error('content') border-red-400 @enderror">{{ old('content', $trend->content) }}</textarea>
            @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                {{ __('common.save_changes') }}
            </button>
            <a href="{{ route('admin.basic-knowledge.index') }}"
               class="px-5 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                {{ __('common.cancel') }}
            </a>
        </div>
    </form>

    {{-- Media --}}
    @php $updateRouteTemplate = route('admin.trend-media.update', ['trendMedia' => 'MEDIA_ID']); @endphp
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden"
         x-data="{
             editId: null,
             editTitle: '',
             editEmbedUrl: '',
             editHasEmbed: false,
             editIsImage: false,
             routeTemplate: @js($updateRouteTemplate),
             addType: 'image',
             addSource: 'file',
         }">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Media</h2>
            <span class="text-sm text-gray-400">{{ $trend->media->count() }} item{{ $trend->media->count() !== 1 ? 's' : '' }}</span>
        </div>

        {{-- Existing items --}}
        @if($trend->media->count())
        <div class="divide-y divide-gray-50">
            @foreach($trend->media as $media)
            <div class="p-4 flex items-start gap-4">

                <div class="w-20 h-14 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                    @if($media->isImage() && $media->file_path)
                        <img src="{{ Storage::url($media->file_path) }}" alt="{{ $media->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-800">
                            <svg class="w-7 h-7 text-white opacity-50" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-0.5">
                        <span class="text-sm font-medium text-gray-800 truncate">{{ $media->title ?: '(no title)' }}</span>
                        <span class="px-1.5 py-0.5 rounded text-xs {{ $media->isImage() ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                            {{ $media->type }}
                        </span>
                        @if($media->isEmbedded())
                        <span class="px-1.5 py-0.5 rounded text-xs bg-gray-100 text-gray-500">embed</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400">Added {{ $media->created_at->format('M d, Y') }}</p>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <button type="button"
                            @click="editId = {{ $media->id }}; editTitle = @js($media->title ?? ''); editEmbedUrl = @js($media->embed_url ?? ''); editHasEmbed = {{ $media->isEmbedded() ? 'true' : 'false' }}; editIsImage = {{ $media->isImage() ? 'true' : 'false' }}"
                            class="px-3 py-1 text-xs text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition">
                        Edit
                    </button>
                    <form method="POST" action="{{ route('admin.trend-media.destroy', $media) }}"
                          onsubmit="return confirm('Delete this media item?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="px-3 py-1 text-xs text-red-500 border border-red-200 rounded-lg hover:bg-red-50 transition">
                            Delete
                        </button>
                    </form>
                </div>

            </div>
            @endforeach
        </div>
        @else
        <div class="px-6 py-8 text-center">
            <p class="text-sm text-gray-400">No media added yet.</p>
        </div>
        @endif

        {{-- Edit modal --}}
        <div x-show="editId !== null" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none">
            <div class="absolute inset-0 bg-black/40" @click="editId = null"></div>
            <div class="relative bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4 z-10">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Edit Media</h3>
                <form :action="routeTemplate.replace('MEDIA_ID', editId)" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Title <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="text" name="title" x-model="editTitle" maxlength="255"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div x-show="editHasEmbed">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Embed URL</label>
                            <input type="url" name="embed_url" x-model="editEmbedUrl" maxlength="500"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <p class="text-xs text-gray-400 mt-1">YouTube and Vimeo URLs are automatically converted to embed format.</p>
                        </div>
                        <div x-show="!editHasEmbed">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Replace file <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="file" name="file"
                                   :accept="editIsImage ? 'image/*' : 'video/mp4,video/webm,video/mov'"
                                   class="w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>
                    <div class="mt-5 flex justify-end gap-2">
                        <button type="button" @click="editId = null"
                                class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Add media form --}}
        <div class="px-6 py-5 border-t border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Add Media</h3>
            <form method="POST" action="{{ route('admin.basic-knowledge.media.store', $trend) }}"
                  enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" x-model="addType"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="image">Image</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                    <div x-show="addType === 'video'">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Source</label>
                        <select x-model="addSource"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="file">Upload file</option>
                            <option value="embed">Embed URL</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Title <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="text" name="title" maxlength="255" placeholder="e.g., Diagram, Tutorial video"
                           value="{{ old('title') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div x-show="addType === 'image' || (addType === 'video' && addSource === 'file')">
                    <label class="block text-xs font-medium text-gray-700 mb-1">File</label>
                    <input type="file" name="file"
                           :accept="addType === 'image' ? 'image/jpeg,image/png,image/gif,image/webp' : 'video/mp4,video/webm,video/quicktime'"
                           class="w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p x-show="addType === 'image'" class="text-xs text-gray-400 mt-1">JPG, PNG, GIF, WebP · max 50 MB</p>
                    <p x-show="addType === 'video' && addSource === 'file'" class="text-xs text-gray-400 mt-1">MP4, WebM, MOV · max 50 MB</p>
                </div>
                <div x-show="addType === 'video' && addSource === 'embed'">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Embed URL</label>
                    <input type="url" name="embed_url" maxlength="500" value="{{ old('embed_url') }}"
                           placeholder="https://www.youtube.com/watch?v=... or Vimeo URL"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <p class="text-xs text-gray-400 mt-1">YouTube and Vimeo URLs are automatically converted to embed format.</p>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                        Add Media
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
