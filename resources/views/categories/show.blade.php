@extends('layouts.app')
@section('title', $category->name)

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-xl font-bold text-gray-800">{{ $category->name }}</h1>
        @if($category->description)
        <p class="text-sm text-gray-500 mt-1">{{ $category->description }}</p>
        @endif
    </div>

    @if($subcategories->count())
    <div class="flex flex-wrap gap-2">
        @foreach($subcategories as $sub)
        <a href="{{ route('categories.show', $sub) }}"
           class="px-3 py-1.5 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 rounded-lg text-sm transition">
            {{ $sub->name }} <span class="text-gray-400 text-xs">({{ $sub->resources_count }})</span>
        </a>
        @endforeach
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">
        @forelse($resources as $doc)
        <a href="{{ route('documents.show', $doc) }}"
           class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition">
            <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-800 text-sm truncate">{{ $doc->title }}</p>
                @if($doc->description)
                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $doc->description }}</p>
                @endif
            </div>
            <span class="text-xs text-gray-400 uppercase font-mono flex-shrink-0">{{ $doc->file_type }}</span>
        </a>
        @empty
        <p class="px-5 py-10 text-center text-gray-400 text-sm">{{ __('documents.no_documents') }}</p>
        @endforelse
    </div>
    <div>{{ $resources->links() }}</div>
</div>
@endsection
