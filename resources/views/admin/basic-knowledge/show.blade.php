@extends('layouts.admin')
@section('title', $trend->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.basic-knowledge.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Basic Knowledge</a>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.basic-knowledge.edit', $trend) }}"
               class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                Edit
            </a>
            <form method="POST" action="{{ route('admin.basic-knowledge.destroy', $trend) }}"
                  onsubmit="return confirm('Delete this entry?')">
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
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                    {{ $trend->category->name }}
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

            {{-- Tags --}}
            @if($trend->tags)
            <div class="flex flex-wrap gap-1.5">
                @foreach($trend->tags as $tag)
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">{{ $tag }}</span>
                @endforeach
            </div>
            @endif

            {{-- Summary --}}
            @if($trend->summary)
            <p class="text-base text-gray-500 leading-relaxed border-l-4 border-indigo-200 pl-4 italic">
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
