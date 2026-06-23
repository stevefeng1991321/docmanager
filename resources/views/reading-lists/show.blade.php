@extends('layouts.app')
@section('title', $readingList->name)

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">{{ $readingList->name }}</h1>
        <a href="{{ route('reading-lists.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; All Lists</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">
        @forelse($readingList->items->sortBy('sort_order') as $item)
        @php $doc = $item->resource; @endphp
        <div class="flex items-center gap-4 px-5 py-4">
            <span class="text-gray-300 text-xs w-4">{{ $loop->iteration }}</span>
            <a href="{{ route('documents.show', $doc) }}" class="flex-1 min-w-0 hover:text-blue-600 transition">
                <p class="font-medium text-gray-800 text-sm truncate">{{ $doc->title }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $doc->category?->name ?? 'Uncategorised' }}</p>
            </a>
            <form method="POST" action="{{ route('reading-lists.items.remove', [$readingList, $doc]) }}">
                @csrf @method('DELETE')
                <button class="text-xs text-red-400 hover:text-red-600">Remove</button>
            </form>
        </div>
        @empty
        <p class="px-5 py-10 text-center text-gray-400 text-sm">This list is empty. Add documents from their detail page.</p>
        @endforelse
    </div>
</div>
@endsection
