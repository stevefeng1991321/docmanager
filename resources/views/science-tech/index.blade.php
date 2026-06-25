@extends('layouts.app')
@section('title', 'Science & Technology')

@section('content')
<div class="space-y-6">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                Science &amp; Technology
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Latest trends shaping the world</p>
        </div>
        <p class="text-sm text-gray-400">{{ $trends->total() }} {{ Str::plural('article', $trends->total()) }}</p>
    </div>

    {{-- Search + Year filter --}}
    <form method="GET" action="{{ route('science-tech.index') }}"
          class="flex flex-col gap-3">

        {{-- Search input --}}
        <div class="relative w-full">
            <input type="text" name="q" value="{{ $q }}"
                   placeholder="Search trends…"
                   class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>

        {{-- Year pills --}}
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('science-tech.index', $q ? ['q' => $q] : []) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-medium transition
                      {{ !$year ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                All years
            </a>
            @foreach($years as $y)
            <a href="{{ route('science-tech.index', array_filter(['q' => $q, 'year' => $y])) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-medium transition
                      {{ (string)$year === (string)$y ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                {{ $y }}
            </a>
            @endforeach
        </div>

    </form>

    {{-- No results --}}
    @if($trends->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-gray-500 font-medium">No trends found</p>
            @if($q || $year)
                <p class="text-sm text-gray-400 mt-1">Try adjusting your search or clearing filters.</p>
                <a href="{{ route('science-tech.index') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">Clear filters</a>
            @endif
        </div>
    @else

    {{-- Cards grid --}}
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @foreach($trends as $trend)
        <a href="{{ route('science-tech.show', $trend) }}"
           class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-blue-200 transition-all flex flex-col overflow-hidden">

            {{-- Card body --}}
            <div class="p-5 flex-1 space-y-3">
                {{-- Year + status badges --}}
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $trend->year }}
                    </span>
                </div>

                {{-- Title --}}
                <h2 class="text-base font-semibold text-gray-900 group-hover:text-blue-700 leading-snug transition line-clamp-2">
                    {{ $trend->title }}
                </h2>

                {{-- Summary --}}
                @if($trend->summary)
                <p class="text-sm text-gray-500 leading-relaxed line-clamp-3">{{ $trend->summary }}</p>
                @endif
            </div>

            {{-- Card footer --}}
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-400">{{ $trend->created_at->format('M j, Y') }}</span>
                <span class="text-xs text-blue-600 font-medium group-hover:underline">Read more →</span>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($trends->hasPages())
    <div class="flex justify-center">
        {{ $trends->links() }}
    </div>
    @endif

    @endif

</div>
@endsection
