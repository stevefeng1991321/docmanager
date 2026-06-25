@extends('layouts.app')
@section('title', $trend->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Back --}}
    <a href="{{ route('basic-knowledge.index') }}" class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Basic Knowledge
    </a>

    {{-- Article --}}
    <article class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8 space-y-5">

            {{-- Meta --}}
            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('basic-knowledge.index', ['category_id' => $trend->category_id]) }}"
                   class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition">
                    {{ $trend->category->name }}
                </a>
                <span class="text-xs text-gray-400">Added {{ $trend->created_at->format('F j, Y') }}</span>
            </div>

            {{-- Title --}}
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-snug">{{ $trend->title }}</h1>

            {{-- Tags --}}
            @if($trend->tags)
            <div class="flex flex-wrap gap-1.5">
                @foreach($trend->tags as $tag)
                    <span class="px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">{{ $tag }}</span>
                @endforeach
            </div>
            @endif

            {{-- Summary --}}
            @if($trend->summary)
            <p class="text-base text-gray-600 leading-relaxed border-l-4 border-indigo-200 pl-4 italic">
                {{ $trend->summary }}
            </p>
            @endif

            <hr class="border-gray-100">

            {{-- Content --}}
            <div class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $trend->content }}</div>

        </div>
    </article>

    {{-- Browse more in category --}}
    <div class="text-sm">
        <a href="{{ route('basic-knowledge.index', ['category_id' => $trend->category_id]) }}"
           class="text-indigo-600 hover:underline">
            Browse more in {{ $trend->category->name }} →
        </a>
    </div>

</div>
@endsection
