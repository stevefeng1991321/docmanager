@extends('layouts.admin')
@section('title', 'Documents')

@section('content')

<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title…"
               class="w-52 border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Status</option>
            @foreach(['draft','pending_review','published','rejected'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
        <select name="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">Filter</button>
        @if(request()->hasAny(['search','status','category']))
            <a href="{{ route('admin.documents.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
        @endif
    </form>
    <div class="flex gap-2">
        <a href="{{ route('admin.documents.trash') }}" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-lg transition">
            Trash
        </a>
        <a href="{{ route('admin.documents.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            + Upload
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Type</th>
                    <th class="px-6 py-3 text-left">Category</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Uploaded By</th>
                    <th class="px-6 py-3 text-left">Date</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($documents as $doc)
                    @php
                        $badge = ['draft'=>'bg-gray-100 text-gray-600','pending_review'=>'bg-yellow-100 text-yellow-700','published'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-600'];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <div class="font-medium text-gray-900 truncate max-w-xs">{{ $doc->title }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $doc->original_filename }}</div>
                        </td>
                        <td class="px-6 py-3 text-gray-500 text-xs uppercase">{{ pathinfo($doc->original_filename, PATHINFO_EXTENSION) }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $doc->category?->name ?? '—' }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badge[$doc->status] ?? '' }}">
                                {{ ucfirst(str_replace('_',' ',$doc->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $doc->uploader?->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-400">{{ $doc->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3">
                            <div class="flex gap-2 items-center">
                                <a href="{{ route('admin.documents.edit', $doc) }}" class="text-blue-600 hover:underline text-xs">Edit</a>
                                @if($doc->status === 'pending_review')
                                    <form action="{{ route('admin.documents.approve', $doc) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-green-600 hover:underline text-xs">Approve</button>
                                    </form>
                                @endif
                                @if(!$doc->locked_by)
                                    <form action="{{ route('admin.documents.lock', $doc) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-yellow-600 hover:underline text-xs">Lock</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.documents.unlock', $doc) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-gray-500 hover:underline text-xs">Unlock</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.documents.destroy', $doc) }}" method="POST"
                                      onsubmit="return confirm('Move to Trash?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:underline text-xs">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">No documents found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($documents->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $documents->links() }}</div>
    @endif
</div>

@endsection
