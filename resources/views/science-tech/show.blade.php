@extends('layouts.app')
@section('title', $trend->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Back --}}
    <a href="{{ route('science-tech.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Science &amp; Technology
    </a>

    {{-- Article --}}
    <article class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8 space-y-5">

            {{-- Meta --}}
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $trend->year }}
                </span>
                <span class="text-xs text-gray-400">Published {{ $trend->created_at->format('F j, Y') }}</span>
            </div>

            {{-- Title --}}
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-snug">{{ $trend->title }}</h1>

            {{-- Summary --}}
            @if($trend->summary)
            <p class="text-base sm:text-lg text-gray-500 leading-relaxed border-l-4 border-blue-300 pl-4 italic">
                {{ $trend->summary }}
            </p>
            @endif

            <hr class="border-gray-100">

            {{-- Content --}}
            <div class="text-gray-700 leading-relaxed whitespace-pre-wrap text-sm sm:text-base">{{ $trend->content }}</div>

        </div>
    </article>

    {{-- Navigation: prev / next (same year) --}}
    @php
        $prev = \App\Models\ScienceTechTrend::where('status', 'published')
                    ->where('id', '<', $trend->id)->orderByDesc('id')->first();
        $next = \App\Models\ScienceTechTrend::where('status', 'published')
                    ->where('id', '>', $trend->id)->orderBy('id')->first();
    @endphp

    @if($prev || $next)
    <div class="flex justify-between gap-4">
        @if($prev)
        <a href="{{ route('science-tech.show', $prev) }}"
           class="flex-1 bg-white border border-gray-200 rounded-xl p-4 hover:border-blue-200 hover:shadow-sm transition group max-w-xs">
            <p class="text-xs text-gray-400 mb-1">← Previous</p>
            <p class="text-sm font-medium text-gray-700 group-hover:text-blue-700 line-clamp-2">{{ $prev->title }}</p>
        </a>
        @else
        <div class="flex-1"></div>
        @endif

        @if($next)
        <a href="{{ route('science-tech.show', $next) }}"
           class="flex-1 bg-white border border-gray-200 rounded-xl p-4 hover:border-blue-200 hover:shadow-sm transition group text-right max-w-xs ml-auto">
            <p class="text-xs text-gray-400 mb-1">Next →</p>
            <p class="text-sm font-medium text-gray-700 group-hover:text-blue-700 line-clamp-2">{{ $next->title }}</p>
        </a>
        @endif
    </div>
    @endif

</div>
@endsection
