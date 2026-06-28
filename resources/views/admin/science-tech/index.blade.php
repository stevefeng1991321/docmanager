@extends('layouts.admin')
@section('title', 'Science & Technology')

@section('content')
<div class="space-y-5">

    {{-- Top bar: filters + new button --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <form method="GET" action="{{ route('admin.science-tech.index') }}" class="flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ $q }}" placeholder="{{ __('admin.science_tech.title_label') }}…"
                   class="w-52 border border-gray-300 rounded-lg px-3 py-2 text-sm">

            <select name="year" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">All Years</option>
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ (string)$year === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>

            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">{{ __('common.all_status') }}</option>
                <option value="published" {{ $status === 'published' ? 'selected' : '' }}>{{ __('common.status_published') }}</option>
                <option value="draft"     {{ $status === 'draft'     ? 'selected' : '' }}>{{ __('common.status_draft') }}</option>
                <option value="archived"  {{ $status === 'archived'  ? 'selected' : '' }}>{{ __('common.status_archived') }}</option>
            </select>

            <select name="sort" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="year_desc"  {{ $sort === 'year_desc'  ? 'selected' : '' }}>Newest Year First</option>
                <option value="year_asc"   {{ $sort === 'year_asc'   ? 'selected' : '' }}>Oldest Year First</option>
                <option value="title_asc"  {{ $sort === 'title_asc'  ? 'selected' : '' }}>Title A–Z</option>
                <option value="title_desc" {{ $sort === 'title_desc' ? 'selected' : '' }}>Title Z–A</option>
                <option value="newest"     {{ $sort === 'newest'     ? 'selected' : '' }}>Recently Added</option>
            </select>

            <button type="submit"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">
                {{ __('common.filter') }}
            </button>

            @if($q || $status || $year || $sort !== 'year_desc')
                <a href="{{ route('admin.science-tech.index') }}"
                   class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">{{ __('common.clear') }}</a>
            @endif
        </form>

        <a href="{{ route('admin.science-tech.create') }}"
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            + {{ __('admin.science_tech.new_article') }}
        </a>
    </div>

    {{-- Results table --}}
    @if($trends->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
            <p class="text-gray-400 text-sm">No trends match your filters.</p>
            <a href="{{ route('admin.science-tech.index') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">Clear filters</a>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wide">
                            <th class="px-5 py-3 text-left font-medium">{{ __('admin.science_tech.col_title') }}</th>
                            <th class="px-4 py-3 text-left font-medium w-20">{{ __('common.year') }}</th>
                            <th class="px-4 py-3 text-left font-medium w-28">{{ __('admin.science_tech.col_status') }}</th>
                            <th class="px-4 py-3 text-left font-medium w-28">{{ __('common.added') }}</th>
                            <th class="px-4 py-3 text-right font-medium w-36">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($trends as $trend)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.science-tech.show', $trend) }}"
                                   class="font-medium text-gray-800 hover:text-blue-600 transition line-clamp-1">
                                    {{ $trend->title }}
                                </a>
                                @if($trend->summary)
                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $trend->summary }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                    {{ $trend->year }}
                                </span>
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
                            <td class="px-4 py-3 text-right text-xs">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.science-tech.show', $trend) }}"
                                       class="text-blue-600 hover:underline">{{ __('admin.science_tech.view_action') }}</a>
                                    <a href="{{ route('admin.science-tech.edit', $trend) }}"
                                       class="text-blue-600 hover:underline">{{ __('admin.science_tech.edit_action') }}</a>
                                    <form method="POST" action="{{ route('admin.science-tech.destroy', $trend) }}"
                                          onsubmit="return confirm('{{ __('admin.science_tech.confirm_delete') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline">{{ __('admin.science_tech.delete_action') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between gap-4 text-xs text-gray-400">
                <span>{{ __('common.showing') }} {{ $trends->firstItem() }}–{{ $trends->lastItem() }} {{ __('common.of') }} {{ $trends->total() }}</span>
                @if($trends->hasPages())
                    {{ $trends->links() }}
                @endif
            </div>
        </div>
    @endif

</div>
@endsection
