@extends('layouts.app')
@section('title', 'Saved Searches')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Saved Searches</h1>
        <a href="{{ route('search') }}" class="text-sm text-blue-600 hover:underline">← Back to Search</a>
    </div>

    @if(session('message'))
    <div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('message') }}</div>
    @endif

    @if($searches->isEmpty())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center text-gray-400 text-sm">
        No saved searches yet. Run a search and click "Save Search" to save it here.
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-100">
        @foreach($searches as $search)
        <div class="flex items-center justify-between px-5 py-3">
            <a href="{{ route('search', ['q' => $search->query]) }}"
               class="text-blue-600 hover:underline text-sm font-medium">
                {{ $search->query }}
            </a>
            <form method="POST" action="{{ route('saved-searches.destroy', $search) }}">
                @csrf @method('DELETE')
                <button class="text-xs text-red-400 hover:text-red-600">Remove</button>
            </form>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
