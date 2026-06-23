@extends('layouts.admin')
@section('title', 'Access Log — ' . $document->title)

@section('content')

<div class="space-y-4">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Access Log</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                <a href="{{ route('admin.documents.edit', $document) }}" class="text-blue-600 hover:underline">{{ $document->title }}</a>
                — {{ number_format($logs->total()) }} event{{ $logs->total() !== 1 ? 's' : '' }}
            </p>
        </div>
        <a href="{{ route('admin.documents.edit', $document) }}"
           class="text-sm text-gray-500 hover:text-gray-700">← Back to document</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($logs->isEmpty())
            <div class="p-8 text-center text-sm text-gray-400">No access events recorded yet.</div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <th class="px-4 py-3 text-left">Date / Time</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Action</th>
                        <th class="px-4 py-3 text-left">Version</th>
                        <th class="px-4 py-3 text-left">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                            {{ $log->created_at?->format('d M Y H:i:s') ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($log->user)
                                <span class="font-medium text-gray-800">{{ $log->user->name }}</span>
                                <span class="text-gray-400 ml-1">@{{ $log->user->username }}</span>
                            @else
                                <span class="text-gray-400 italic">Guest / deleted</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match($log->action) {
                                    'view'     => 'bg-blue-50 text-blue-700',
                                    'download' => 'bg-green-50 text-green-700',
                                    'preview'  => 'bg-purple-50 text-purple-700',
                                    default    => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $log->version ? 'v' . $log->version->version_number : '—' }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-400">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($logs->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

@endsection
