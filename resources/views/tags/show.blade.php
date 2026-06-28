@extends('layouts.app')
@section('title', __('tags.heading') . ': ' . $tag->name)

@section('content')
<div class="space-y-5">
    <h1 class="text-xl font-bold text-gray-800">{{ __('tags.heading') }}: <span class="text-blue-600">{{ $tag->name }}</span></h1>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">
        @forelse($resources as $doc)
        <a href="{{ route('documents.show', $doc) }}"
           class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition">
            <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-800 text-sm truncate">{{ $doc->title }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $doc->category?->name ?? __('categories.all') }}</p>
            </div>
            <span class="text-xs text-gray-400 uppercase font-mono flex-shrink-0">{{ $doc->file_type }}</span>
        </a>
        @empty
        <p class="px-5 py-10 text-center text-gray-400 text-sm">{{ __('tags.empty') }}</p>
        @endforelse
    </div>
    <div>{{ $resources->links() }}</div>
</div>
@endsection
