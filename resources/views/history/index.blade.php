@extends('layouts.app')
@section('title', 'Recently Viewed')

@section('content')
<div class="space-y-4">
    <h1 class="text-xl font-bold text-gray-800">Recently Viewed</h1>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">
        @forelse($history as $item)
        @php
            $isDoc = $item->viewable_type === \App\Models\Resource::class;
            $viewable = $item->viewable;
            $url = $isDoc
                ? route('documents.show', $viewable)
                : route('basic-knowledge.show', $viewable);
        @endphp
        <a href="{{ $url }}"
           class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    @if($isDoc)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">Doc</span>
                    @else
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-700">Knowledge</span>
                    @endif
                    <p class="font-medium text-gray-800 text-sm truncate">{{ $viewable?->title ?? '—' }}</p>
                </div>
                <p class="text-xs text-gray-400">{{ $viewable?->category?->name ?? 'Uncategorised' }}</p>
            </div>
            <span class="text-xs text-gray-400 flex-shrink-0">{{ $item->viewed_at->diffForHumans() }}</span>
        </a>
        @empty
        <p class="px-5 py-10 text-center text-gray-400 text-sm">No viewing history yet.</p>
        @endforelse
    </div>
</div>
@endsection
