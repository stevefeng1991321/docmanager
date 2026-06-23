@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $cards = [
            ['label' => 'Total Documents', 'value' => number_format($stats['total_documents']), 'color' => 'blue',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'Active Users',    'value' => number_format($stats['total_users']),     'color' => 'green',  'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['label' => 'Downloads Today', 'value' => number_format($stats['downloads_today']), 'color' => 'purple', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'],
            ['label' => 'Storage Used',    'value' => number_format($stats['storage_bytes'] / 1048576, 1) . ' MB', 'color' => 'yellow', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
        ];
        $colors = ['blue' => 'bg-blue-50 text-blue-700', 'green' => 'bg-green-50 text-green-700', 'purple' => 'bg-purple-50 text-purple-700', 'yellow' => 'bg-yellow-50 text-yellow-700'];
    @endphp

    @foreach($cards as $card)
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $card['label'] }}</span>
                <div class="w-9 h-9 rounded-lg {{ $colors[$card['color']] }} flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
        </div>
    @endforeach
</div>

{{-- Alert banners --}}
@if($stats['pending_users'] > 0)
    <div class="mb-4 flex items-center justify-between p-4 bg-amber-50 border border-amber-200 rounded-xl">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span class="text-sm font-medium text-amber-800">
                <strong>{{ $stats['pending_users'] }}</strong> account(s) awaiting activation
            </span>
        </div>
        <a href="{{ route('admin.users.pending') }}" class="text-sm font-semibold text-amber-700 hover:underline">Review →</a>
    </div>
@endif

@if($stats['pending_review'] > 0)
    <div class="mb-6 flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-xl">
        <span class="text-sm font-medium text-blue-800">
            <strong>{{ $stats['pending_review'] }}</strong> document(s) awaiting review
        </span>
        <a href="{{ route('admin.documents.index', ['status' => 'pending_review']) }}" class="text-sm font-semibold text-blue-700 hover:underline">Review →</a>
    </div>
@endif

{{-- Recent documents --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-800">Recent Documents</h2>
        <a href="{{ route('admin.documents.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Category</th>
                    <th class="px-6 py-3 text-left">Uploaded By</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentDocuments as $doc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <a href="{{ route('admin.documents.edit', $doc) }}" class="font-medium text-blue-600 hover:underline">
                                {{ Str::limit($doc->title, 50) }}
                            </a>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $doc->category?->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $doc->uploader?->name ?? '—' }}</td>
                        <td class="px-6 py-3">
                            @php
                                $badge = ['draft' => 'bg-gray-100 text-gray-600', 'pending_review' => 'bg-yellow-100 text-yellow-700', 'published' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700'];
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badge[$doc->status] ?? '' }}">
                                {{ ucfirst(str_replace('_', ' ', $doc->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-400">{{ $doc->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">No documents yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
