@extends('layouts.admin')
@section('title', $trend->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.science-tech.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Trends</a>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.science-tech.edit', $trend) }}"
               class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                Edit
            </a>
            <form method="POST" action="{{ route('admin.science-tech.destroy', $trend) }}"
                  onsubmit="return confirm('Delete this trend?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="px-4 py-1.5 border border-red-200 text-red-500 hover:bg-red-50 text-sm font-medium rounded-lg transition">
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Article card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 space-y-4">

            {{-- Meta row --}}
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $trend->year }}
                </span>
                @php
                    $badge = match($trend->status) {
                        'published' => 'bg-green-100 text-green-700',
                        'archived'  => 'bg-amber-100 text-amber-700',
                        default     => 'bg-gray-100 text-gray-600',
                    };
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $badge }}">
                    {{ ucfirst($trend->status) }}
                </span>
                <span class="text-xs text-gray-400 ml-auto">Added {{ $trend->created_at->format('F j, Y') }}</span>
            </div>

            {{-- Title --}}
            <h1 class="text-2xl font-bold text-gray-900 leading-snug">{{ $trend->title }}</h1>

            {{-- Summary --}}
            @if($trend->summary)
            <p class="text-base text-gray-500 leading-relaxed border-l-4 border-blue-200 pl-4 italic">
                {{ $trend->summary }}
            </p>
            @endif

            <hr class="border-gray-100">

            {{-- Content --}}
            <div class="text-gray-700 leading-relaxed whitespace-pre-wrap text-sm">{{ $trend->content }}</div>

        </div>
    </div>

</div>
@endsection
