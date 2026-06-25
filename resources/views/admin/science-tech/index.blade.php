@extends('layouts.admin')
@section('title', 'Science & Technology')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Science &amp; Technology Trends</h1>
            <p class="text-sm text-gray-500 mt-0.5">Latest trends organized by year</p>
        </div>
        <a href="{{ route('admin.science-tech.create') }}"
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            + New Trend
        </a>
    </div>

    @if($byYear->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
            <p class="text-gray-400 text-sm">No trends yet. Add the first one.</p>
        </div>
    @else
        @foreach($byYear as $year => $trends)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

            {{-- Year header --}}
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-semibold text-gray-700">{{ $year }}</span>
                <span class="text-xs text-gray-400">({{ $trends->count() }} {{ Str::plural('article', $trends->count()) }})</span>
            </div>

            {{-- Table --}}
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wide">
                        <th class="px-5 py-2.5 text-left font-medium">Title</th>
                        <th class="px-4 py-2.5 text-left font-medium">Status</th>
                        <th class="px-4 py-2.5 text-left font-medium">Added</th>
                        <th class="px-4 py-2.5 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($trends as $trend)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.science-tech.show', $trend) }}"
                               class="font-medium text-gray-800 hover:text-blue-600 transition">
                                {{ $trend->title }}
                            </a>
                            @if($trend->summary)
                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $trend->summary }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match($trend->status) {
                                    'published' => 'bg-green-100 text-green-700',
                                    'archived'  => 'bg-amber-100 text-amber-700',
                                    default     => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $badge }}">
                                {{ ucfirst($trend->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs whitespace-nowrap">
                            {{ $trend->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.science-tech.show', $trend) }}"
                                   class="text-xs text-gray-500 hover:text-blue-600 transition">View</a>
                                <a href="{{ route('admin.science-tech.edit', $trend) }}"
                                   class="text-xs text-gray-500 hover:text-blue-600 transition">Edit</a>
                                <form method="POST" action="{{ route('admin.science-tech.destroy', $trend) }}"
                                      onsubmit="return confirm('Delete this trend?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-gray-400 hover:text-red-500 transition">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
    @endif

</div>
@endsection
