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

    {{-- Media --}}
    @if($trend->media->count())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden"
         x-data="{ lightboxSrc: null, lightboxAlt: '', zoom: 1 }"
         @keydown.escape.window="lightboxSrc = null; zoom = 1">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Media <span class="text-gray-400 font-normal text-sm">({{ $trend->media->count() }})</span></h2>
        </div>
        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($trend->media as $media)
            <div class="rounded-xl overflow-hidden border border-gray-100 bg-gray-50">
                @if($media->isImage() && $media->file_path)
                    <img src="{{ Storage::url($media->file_path) }}" alt="{{ $media->title }}"
                         class="w-full object-cover max-h-56 cursor-pointer hover:opacity-90 transition"
                         @click="lightboxSrc = '{{ Storage::url($media->file_path) }}'; lightboxAlt = @js($media->title ?? ''); zoom = 1">
                @elseif($media->isEmbedded())
                    <div class="aspect-video">
                        <iframe src="{{ $media->embed_url }}" class="w-full h-full" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen loading="lazy"></iframe>
                    </div>
                @elseif($media->isVideo() && $media->file_path)
                    <video src="{{ Storage::url($media->file_path) }}" controls
                           class="w-full max-h-56 bg-black"></video>
                @endif
                @if($media->title)
                <p class="px-3 py-2 text-xs text-gray-500">{{ $media->title }}</p>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Lightbox --}}
        <div x-show="lightboxSrc"
             class="fixed inset-0 z-50 flex flex-col bg-black/85"
             style="display:none"
             @wheel.prevent="zoom = Math.min(4, Math.max(0.5, +(zoom + ($event.deltaY < 0 ? 0.25 : -0.25)).toFixed(2)))">

            {{-- Top bar --}}
            <div class="flex items-center justify-end px-4 py-3 flex-shrink-0">
                <button @click="lightboxSrc = null; zoom = 1"
                        class="flex items-center gap-1.5 text-white/70 hover:text-white text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Close
                </button>
            </div>

            {{-- Image --}}
            <div class="flex-1 flex items-center justify-center overflow-hidden"
                 @click.self="lightboxSrc = null; zoom = 1">
                <img :src="lightboxSrc" :alt="lightboxAlt"
                     :style="{ transform: 'scale(' + zoom + ')', transition: 'transform 0.15s ease', transformOrigin: 'center center' }"
                     class="max-h-[78vh] max-w-[88vw] object-contain rounded-lg shadow-2xl select-none"
                     draggable="false"
                     @click.stop>
            </div>

            {{-- Bottom bar: zoom controls + caption --}}
            <div class="flex items-center justify-between px-4 py-3 flex-shrink-0">
                <p x-show="lightboxAlt" x-text="lightboxAlt" class="text-white/60 text-sm truncate max-w-xs"></p>
                <div class="flex items-center gap-1.5 ml-auto">
                    <button @click="zoom = Math.max(0.5, +(zoom - 0.25).toFixed(2))"
                            :disabled="zoom <= 0.5"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 text-white text-xl transition disabled:opacity-30 select-none">
                        −
                    </button>
                    <button @click="zoom = 1"
                            title="Reset to 100%"
                            class="px-2 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs transition min-w-[3.5rem] text-center tabular-nums">
                        <span x-text="Math.round(zoom * 100) + '%'"></span>
                    </button>
                    <button @click="zoom = Math.min(4, +(zoom + 0.25).toFixed(2))"
                            :disabled="zoom >= 4"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 text-white text-xl transition disabled:opacity-30 select-none">
                        +
                    </button>
                </div>
            </div>

        </div>
    </div>
    @endif

</div>
@endsection
