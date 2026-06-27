@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

{{-- Stat cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    @php
        $storageBytes = $stats['storage_bytes'] ?? 0;
        $storageFmt   = $storageBytes >= 1073741824
            ? number_format($storageBytes / 1073741824, 2) . ' GB'
            : number_format($storageBytes / 1048576, 1) . ' MB';
        $cards = [
            ['label' => 'Total Documents',    'value' => number_format($stats['total_documents']),   'color' => 'blue',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'Uploaded This Week', 'value' => number_format($stats['uploads_this_week']), 'color' => 'indigo', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'],
            ['label' => 'Active Users',        'value' => number_format($stats['total_users']),       'color' => 'green',  'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['label' => 'Downloads Today',     'value' => number_format($stats['downloads_today']),   'color' => 'purple', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'],
            ['label' => 'Storage Used',        'value' => $storageFmt,                               'color' => 'yellow', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
            ['label' => 'Failed Jobs',         'value' => number_format($stats['failed_jobs']),       'color' => $stats['failed_jobs'] > 0 ? 'red' : 'gray', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];
        $colors = ['blue' => 'bg-blue-50 text-blue-700', 'indigo' => 'bg-indigo-50 text-indigo-700', 'green' => 'bg-green-50 text-green-700', 'purple' => 'bg-purple-50 text-purple-700', 'yellow' => 'bg-yellow-50 text-yellow-700', 'red' => 'bg-red-50 text-red-700', 'gray' => 'bg-gray-50 text-gray-500'];
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

{{-- Knowledge Base overview --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-700">Knowledge Base</h3>
        <a href="{{ route('admin.basic-knowledge.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">View all →</a>
    </div>
    <div class="grid grid-cols-3 gap-4 mb-4">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Total Entries</p>
            <p class="text-xl font-bold text-gray-900">{{ number_format($knowledgeStats['total']) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Published</p>
            <p class="text-xl font-bold text-green-600">{{ number_format($knowledgeStats['published']) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Draft</p>
            <p class="text-xl font-bold text-gray-400">{{ number_format($knowledgeStats['draft']) }}</p>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
        {{-- By category --}}
        @if($knowledgeStats['by_category']->isNotEmpty())
        <div>
            <p class="text-xs font-medium text-gray-500 mb-2">Published by Category</p>
            <div class="space-y-1.5">
                @foreach($knowledgeStats['by_category'] as $cat)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">{{ $cat->name }}</span>
                    <span class="text-xs font-semibold text-indigo-600">{{ $cat->count }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        {{-- Recently added --}}
        @if($knowledgeStats['recent']->isNotEmpty())
        <div>
            <p class="text-xs font-medium text-gray-500 mb-2">Recently Added</p>
            <div class="space-y-1.5">
                @foreach($knowledgeStats['recent'] as $entry)
                <div class="flex items-start justify-between gap-2 text-sm">
                    <a href="{{ route('admin.basic-knowledge.show', $entry) }}"
                       class="text-gray-700 hover:text-blue-600 transition truncate">{{ $entry->title }}</a>
                    <span class="text-xs text-gray-400 whitespace-nowrap flex-shrink-0">{{ $entry->created_at->format('d M') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Employees overview --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-700">Employees</h3>
        <a href="{{ route('admin.employees.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">View all →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Total</p>
            <p class="text-xl font-bold text-gray-900">{{ number_format($employeeStats['total']) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Active</p>
            <p class="text-xl font-bold text-green-600">{{ number_format($employeeStats['active']) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Inactive</p>
            <p class="text-xl font-bold text-gray-500">{{ number_format($employeeStats['inactive']) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">New Hires (30d)</p>
            <p class="text-xl font-bold text-blue-600">{{ number_format($employeeStats['recent_hires']) }}</p>
        </div>
    </div>
    @if($employeeStats['by_department']->isNotEmpty())
    <div class="mt-4 pt-4 border-t border-gray-100">
        <p class="text-xs font-medium text-gray-500 mb-2">Top Departments</p>
        <div class="space-y-1.5">
            @foreach($employeeStats['by_department'] as $dept)
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">{{ $dept->name }}</span>
                <span class="text-gray-400">{{ $dept->employees_count }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Work Reports overview --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-700">Work Reports</h3>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.work-reports.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">View all →</a>
        @endif
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-6 gap-4">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Total</p>
            <p class="text-xl font-bold text-gray-900">{{ number_format($workReportStats['total']) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Today</p>
            <p class="text-xl font-bold text-blue-600">{{ number_format($workReportStats['submitted_today']) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Pending Review</p>
            <p class="text-xl font-bold text-amber-600">{{ number_format($workReportStats['pending_review']) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Approved</p>
            <p class="text-xl font-bold text-green-600">{{ number_format($workReportStats['approved']) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Rejected</p>
            <p class="text-xl font-bold text-red-600">{{ number_format($workReportStats['rejected']) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Draft</p>
            <p class="text-xl font-bold text-gray-500">{{ number_format($workReportStats['draft']) }}</p>
        </div>
    </div>
    @if($workReportStats['recent']->isNotEmpty())
    <div class="mt-4 pt-4 border-t border-gray-100">
        <p class="text-xs font-medium text-gray-500 mb-2">Recent Activity</p>
        <div class="space-y-1.5">
            @foreach($workReportStats['recent'] as $report)
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">{{ $report->employee->full_name }} — {{ $report->title }}</span>
                <span class="text-gray-400">{{ $report->submitted_at->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Charts row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

    {{-- Upload trend (7 days) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 col-span-1">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Uploads — Last 7 Days</h3>
        @php
            $maxCount = $uploadTrend->max('count') ?: 1;
        @endphp
        <div class="flex items-end gap-1.5 h-24">
            @forelse($uploadTrend as $day)
            @php $pct = round(($day->count / $maxCount) * 100); @endphp
            <div class="flex-1 flex flex-col items-center gap-1" title="{{ $day->date }}: {{ $day->count }}">
                <span class="text-xs text-gray-400">{{ $day->count }}</span>
                <div class="w-full bg-blue-500 rounded-t" style="height: {{ max(4, $pct) }}%"></div>
                <span class="text-xs text-gray-400 rotate-45 origin-left" style="font-size:9px">{{ \Carbon\Carbon::parse($day->date)->format('d/m') }}</span>
            </div>
            @empty
            <p class="text-xs text-gray-400 w-full text-center">No uploads this week.</p>
            @endforelse
        </div>
    </div>

    {{-- Download trend (7 days) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 col-span-1">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Downloads — Last 7 Days</h3>
        @php $maxDl = $downloadTrend->max('count') ?: 1; @endphp
        <div class="flex items-end gap-1.5 h-24">
            @forelse($downloadTrend as $day)
            @php $pct = round(($day->count / $maxDl) * 100); @endphp
            <div class="flex-1 flex flex-col items-center gap-1" title="{{ $day->date }}: {{ $day->count }}">
                <span class="text-xs text-gray-400">{{ $day->count }}</span>
                <div class="w-full bg-purple-500 rounded-t" style="height: {{ max(4, $pct) }}%"></div>
                <span class="text-xs text-gray-400" style="font-size:9px">{{ \Carbon\Carbon::parse($day->date)->format('d/m') }}</span>
            </div>
            @empty
            <p class="text-xs text-gray-400 w-full text-center">No downloads this week.</p>
            @endforelse
        </div>
    </div>

    {{-- Top search terms --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 col-span-1">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Top Searches (30 days)</h3>
        @forelse($topSearches as $s)
        <div class="flex items-center justify-between py-1 border-b border-gray-50 last:border-0">
            <span class="text-xs text-gray-700 truncate max-w-[70%]">{{ $s->query }}</span>
            <span class="text-xs font-semibold text-gray-500">{{ $s->count }}×</span>
        </div>
        @empty
        <p class="text-xs text-gray-400">No searches yet.</p>
        @endforelse
    </div>

</div>

{{-- Top downloaded --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">Top Downloaded Documents</h3>
    @php $maxDlDoc = $topDownloaded->max('download_count') ?: 1; @endphp
    @forelse($topDownloaded as $doc)
    <div class="flex items-center gap-3 py-1.5 border-b border-gray-50 last:border-0">
        <div class="flex-1 min-w-0">
            <a href="{{ route('admin.documents.edit', $doc) }}" class="text-sm text-blue-600 hover:underline truncate block">{{ $doc->title }}</a>
        </div>
        <div class="w-32 bg-gray-100 rounded-full h-2">
            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ round($doc->download_count / $maxDlDoc * 100) }}%"></div>
        </div>
        <span class="text-xs text-gray-500 w-10 text-right">{{ number_format($doc->download_count) }}</span>
    </div>
    @empty
    <p class="text-xs text-gray-400">No downloads yet.</p>
    @endforelse
</div>

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
                                $badge = ['draft' => 'bg-gray-100 text-gray-600', 'pending_review' => 'bg-yellow-100 text-yellow-700', 'published' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700', 'archived' => 'bg-gray-100 text-gray-500'];
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
