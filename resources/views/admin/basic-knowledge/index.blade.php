@extends('layouts.admin')
@section('title', 'Basic Knowledge')

@section('content')
<div class="space-y-5">

    {{-- Top bar: filters + new button --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <form method="GET" action="{{ route('admin.basic-knowledge.index') }}" class="flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ $q }}" placeholder="Search title…"
                   class="w-52 border border-gray-300 rounded-lg px-3 py-2 text-sm">

            <select name="category_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ (string)$category_id === (string)$cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">All Status</option>
                <option value="published" {{ $status === 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft"     {{ $status === 'draft'     ? 'selected' : '' }}>Draft</option>
                <option value="archived"  {{ $status === 'archived'  ? 'selected' : '' }}>Archived</option>
            </select>

            <select name="sort" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="newest"     {{ $sort === 'newest'     ? 'selected' : '' }}>Recently Added</option>
                <option value="oldest"     {{ $sort === 'oldest'     ? 'selected' : '' }}>Oldest First</option>
                <option value="title_asc"  {{ $sort === 'title_asc'  ? 'selected' : '' }}>Title A–Z</option>
                <option value="title_desc" {{ $sort === 'title_desc' ? 'selected' : '' }}>Title Z–A</option>
            </select>

            <button type="submit"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">
                Filter
            </button>

            @if($q || $status || $category_id || $sort !== 'newest')
                <a href="{{ route('admin.basic-knowledge.index') }}"
                   class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
            @endif
        </form>

        <a href="{{ route('admin.basic-knowledge.create') }}"
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            + New Entry
        </a>
    </div>

    {{-- Results table --}}
    @if($trends->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="text-gray-400 text-sm">No entries match your filters.</p>
            <a href="{{ route('admin.basic-knowledge.index') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">Clear filters</a>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wide">
                            <th class="px-5 py-3 text-left font-medium">Title</th>
                            <th class="px-4 py-3 text-left font-medium w-40">Category</th>
                            <th class="px-4 py-3 text-left font-medium w-28">Status</th>
                            <th class="px-4 py-3 text-left font-medium w-28">Added</th>
                            <th class="px-4 py-3 text-right font-medium w-36">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($trends as $trend)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.basic-knowledge.show', $trend) }}"
                                   class="font-medium text-gray-800 hover:text-blue-600 transition line-clamp-1">
                                    {{ $trend->title }}
                                </a>
                                @if($trend->summary)
                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $trend->summary }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                    {{ $trend->category->name }}
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
                                    <a href="{{ route('admin.basic-knowledge.show', $trend) }}"
                                       class="text-blue-600 hover:underline">View</a>
                                    <a href="{{ route('admin.basic-knowledge.edit', $trend) }}"
                                       class="text-blue-600 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.basic-knowledge.destroy', $trend) }}"
                                          onsubmit="return confirm('Delete this entry?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline">Delete</button>
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
                <span>Showing {{ $trends->firstItem() }}–{{ $trends->lastItem() }} of {{ $trends->total() }}</span>
                @if($trends->hasPages())
                    {{ $trends->links() }}
                @endif
            </div>
        </div>
    @endif

</div>
@endsection
