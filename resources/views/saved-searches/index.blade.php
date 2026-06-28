@extends('layouts.app')
@section('title', __('saved_searches.heading'))

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">{{ __('saved_searches.heading') }}</h1>
        <a href="{{ route('search') }}" class="text-sm text-blue-600 hover:underline">← Back to Search</a>
    </div>

    @if(session('message'))
    <div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('message') }}</div>
    @endif

    @if($searches->isEmpty())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center text-gray-400 text-sm">
        {{ __('saved_searches.empty') }} {{ __('saved_searches.empty_sub') }}
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-100">
        @foreach($searches as $search)
        <div class="px-5 py-3" x-data="{ editing: false, name: '{{ addslashes($search->name) }}' }">
            <div class="flex items-center justify-between gap-3">

                {{-- View mode --}}
                <div x-show="!editing" class="flex-1 min-w-0">
                    <a href="{{ route('search', ['q' => $search->query]) }}"
                       class="text-blue-600 hover:underline text-sm font-medium block truncate"
                       title="{{ $search->query }}">
                        {{ $search->name }}
                    </a>
                    @if($search->name !== $search->query)
                        <p class="text-xs text-gray-400 truncate mt-0.5">{{ $search->query }}</p>
                    @endif
                </div>

                {{-- Edit mode --}}
                <form x-show="editing" method="POST"
                      action="{{ route('saved-searches.update', $search) }}"
                      class="flex-1 flex gap-2" @submit.prevent="$el.submit()">
                    @csrf @method('PUT')
                    <input type="text" name="name" x-model="name" maxlength="100" required
                           class="flex-1 border border-blue-300 rounded-lg px-3 py-1.5 text-sm focus:ring-blue-500 focus:border-blue-500"
                           @keydown.escape="editing = false">
                    <button type="submit"
                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg">
                        {{ __('common.save') }}
                    </button>
                    <button type="button" @click="editing = false"
                            class="px-3 py-1.5 border border-gray-200 hover:bg-gray-50 text-gray-500 text-xs rounded-lg">
                        {{ __('common.cancel') }}
                    </button>
                </form>

                {{-- Actions --}}
                <div x-show="!editing" class="flex items-center gap-3 flex-shrink-0">
                    <button @click="editing = true; $nextTick(() => $el.closest('.px-5').querySelector('input[name=name]').focus())"
                            class="text-xs text-gray-400 hover:text-gray-600">
                        {{ __('common.rename') }}
                    </button>
                    <form method="POST" action="{{ route('saved-searches.destroy', $search) }}">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-600">{{ __('common.remove') }}</button>
                    </form>
                </div>

            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
