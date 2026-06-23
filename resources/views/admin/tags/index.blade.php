@extends('layouts.admin')
@section('title', 'Tags')

@section('content')
<div x-data="{ showForm: false }" class="space-y-5">

    <button @click="showForm = !showForm"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
        + Add Tag
    </button>

    <div x-show="showForm" x-cloak class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <form method="POST" action="{{ route('admin.tags.store') }}" class="flex gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tag Name</label>
                <input type="text" name="name" required placeholder="e.g. networking"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-52">
            </div>
            <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                Save
            </button>
        </form>
    </div>

    <div class="flex flex-wrap gap-2">
        @forelse ($tags as $tag)
        <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-full px-3 py-1 text-sm shadow-sm">
            <span class="text-gray-700">{{ $tag->name }}</span>
            <span class="text-xs text-gray-400">({{ $tag->resources_count }})</span>
            <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}"
                  onsubmit="return confirm('Delete tag?')" class="inline">
                @csrf @method('DELETE')
                <button class="ml-1 text-gray-300 hover:text-red-500 leading-none">&times;</button>
            </form>
        </div>
        @empty
        <p class="text-gray-400 text-sm">No tags yet.</p>
        @endforelse
    </div>

</div>
@endsection
