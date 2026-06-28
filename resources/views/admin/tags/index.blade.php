@extends('layouts.admin')
@section('title', __('admin.tags.heading'))

@section('content')
<div x-data="{ showForm: false, showMerge: false }" class="space-y-5">

    <div class="flex gap-2">
        <button @click="showForm = !showForm"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
            + {{ __('admin.tags.new_tag') }}
        </button>
        <button @click="showMerge = !showMerge"
                class="px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
            {{ __('common.merge') }}
        </button>
    </div>

    <div x-show="showMerge" x-cloak class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <form method="POST" action="{{ route('admin.tags.merge') }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('admin.tags.name_label') }}</label>
                <select name="source_id" required class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-44">
                    <option value="">Select source</option>
                    @foreach($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }} ({{ $tag->resources_count }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">…into this tag</label>
                <select name="target_id" required class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-44">
                    <option value="">Select target</option>
                    @foreach($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" onclick="return confirm('Merge tags? The source tag will be deleted.')"
                    class="px-5 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition">
                {{ __('common.merge') }}
            </button>
        </form>
    </div>

    <div x-show="showForm" x-cloak class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <form method="POST" action="{{ route('admin.tags.store') }}" class="flex gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('admin.tags.name_label') }}</label>
                <input type="text" name="name" required placeholder="{{ __('admin.tags.name_placeholder') }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-52">
            </div>
            <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                {{ __('common.save') }}
            </button>
        </form>
    </div>

    <div class="flex flex-wrap gap-2">
        @forelse ($tags as $tag)
        <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-full px-3 py-1 text-sm shadow-sm">
            <span class="text-gray-700">{{ $tag->name }}</span>
            <span class="text-xs text-gray-400">({{ $tag->resources_count }})</span>
            <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}"
                  onsubmit="return confirm('{{ __('admin.tags.confirm_delete', ['name' => '']) }}')" class="inline">
                @csrf @method('DELETE')
                <button class="ml-1 text-gray-300 hover:text-red-500 leading-none">&times;</button>
            </form>
        </div>
        @empty
        <p class="text-gray-400 text-sm">{{ __('admin.tags.no_tags') }}</p>
        @endforelse
    </div>

</div>
@endsection
