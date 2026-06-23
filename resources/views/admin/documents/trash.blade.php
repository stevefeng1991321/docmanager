@extends('layouts.admin')
@section('title', 'Trash')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-gray-800">Trash</h2>
            <p class="text-sm text-gray-400 mt-0.5">Documents are permanently deleted after {{ config('app.trash_retention_days', 30) }} days.</p>
        </div>
        <a href="{{ route('admin.documents.index') }}" class="text-sm text-blue-600 hover:underline">← Back to Documents</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Uploaded By</th>
                    <th class="px-6 py-3 text-left">Deleted</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($documents as $doc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <div class="font-medium text-gray-700">{{ $doc->title }}</div>
                            <div class="text-xs text-gray-400">{{ $doc->original_filename }}</div>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $doc->uploader?->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-400">{{ $doc->deleted_at->diffForHumans() }}</td>
                        <td class="px-6 py-3">
                            <div class="flex gap-3">
                                <form action="{{ route('admin.documents.restore', $doc->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="text-green-600 hover:underline text-xs">Restore</button>
                                </form>
                                <form action="{{ route('admin.documents.force-delete', $doc->id) }}" method="POST"
                                      onsubmit="return confirm('Permanently delete? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline text-xs">Delete Forever</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400">Trash is empty.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($documents->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $documents->links() }}</div>
    @endif
</div>

@endsection
