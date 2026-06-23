@extends('layouts.app')
@section('title', 'Reading Lists')

@section('content')
<div class="space-y-5" x-data="{ showForm: false }">

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Reading Lists</h1>
        <button @click="showForm = !showForm"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
            + New List
        </button>
    </div>

    <div x-show="showForm" x-cloak class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <form method="POST" action="{{ route('reading-lists.store') }}" class="flex gap-2">
            @csrf
            <input type="text" name="name" required placeholder="List name"
                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                Create
            </button>
        </form>
    </div>

    @forelse($lists as $list)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center justify-between">
        <div>
            <a href="{{ route('reading-lists.show', $list) }}"
               class="font-semibold text-gray-800 hover:text-blue-600 transition">{{ $list->name }}</a>
            <p class="text-xs text-gray-400 mt-0.5">{{ $list->items_count }} document(s)</p>
        </div>
        <form method="POST" action="{{ route('reading-lists.destroy', $list) }}"
              onsubmit="return confirm('Delete this list?')">
            @csrf @method('DELETE')
            <button class="text-xs text-red-400 hover:text-red-600 transition">Delete</button>
        </form>
    </div>
    @empty
    <p class="text-center text-gray-400 text-sm py-10">No reading lists yet.</p>
    @endforelse
</div>
@endsection
