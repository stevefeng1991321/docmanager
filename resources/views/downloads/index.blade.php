@extends('layouts.app')
@section('title', 'Download History')

@section('content')
<div class="space-y-4">
    <h1 class="text-xl font-bold text-gray-800">Download History</h1>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">
        @forelse($downloads as $dl)
        @php $doc = $dl->resource; @endphp
        <a href="{{ route('documents.show', $doc) }}"
           class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition">
            <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-800 text-sm truncate">{{ $doc->title }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $doc->category?->name ?? 'Uncategorised' }}</p>
            </div>
            <span class="text-xs text-gray-400 flex-shrink-0">{{ $dl->created_at->diffForHumans() }}</span>
        </a>
        @empty
        <p class="px-5 py-10 text-center text-gray-400 text-sm">No downloads yet.</p>
        @endforelse
    </div>
    <div>{{ $downloads->links() }}</div>
</div>
@endsection
