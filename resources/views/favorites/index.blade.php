@extends('layouts.app')
@section('title', __('favorites.heading'))

@section('content')
<div class="space-y-4">
    <h1 class="text-xl font-bold text-gray-800">{{ __('favorites.heading') }}</h1>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">
        @forelse ($favorites as $fav)
        @php $doc = $fav->resource; @endphp
        <div class="flex items-center gap-4 px-5 py-4">
            <a href="{{ route('documents.show', $doc) }}" class="flex-1 min-w-0 hover:text-blue-600 transition">
                <p class="font-medium text-gray-800 text-sm truncate">{{ $doc->title }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $doc->category?->name ?? 'Uncategorised' }} &middot; {{ $doc->created_at->format('Y-m-d') }}</p>
            </a>
            <form method="POST" action="{{ route('favorites.destroy', $doc) }}">
                @csrf @method('DELETE')
                <button class="text-xs text-red-400 hover:text-red-600 transition">{{ __('favorites.remove') }}</button>
            </form>
        </div>
        @empty
        <p class="px-5 py-10 text-center text-gray-400 text-sm">{{ __('favorites.empty') }} {{ __('favorites.empty_sub') }}</p>
        @endforelse
    </div>
    <div>{{ $favorites->links() }}</div>
</div>
@endsection
