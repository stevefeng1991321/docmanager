@extends('layouts.admin')
@section('title', 'Compare: ' . $a->title . ' vs ' . $b->title)

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Document Comparison</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                <a href="{{ route('admin.compare.index') }}" class="text-blue-600 hover:underline">Change selection</a>
            </p>
        </div>
        <div class="flex items-center gap-2 text-xs text-gray-500">
            <span class="inline-block w-3 h-3 rounded-sm bg-red-100 border border-red-300"></span> Removed &nbsp;
            <span class="inline-block w-3 h-3 rounded-sm bg-green-100 border border-green-300"></span> Added &nbsp;
            <span class="inline-block w-3 h-3 rounded-sm bg-gray-50 border border-gray-200"></span> Unchanged
        </div>
    </div>

    {{-- Document titles --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
            <p class="text-xs font-semibold text-blue-500 uppercase tracking-wide mb-0.5">Document A</p>
            <p class="text-sm font-medium text-gray-800 truncate">{{ $a->title }}</p>
            <p class="text-xs text-gray-500">ID #{{ $a->id }}</p>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-xl px-4 py-3">
            <p class="text-xs font-semibold text-purple-500 uppercase tracking-wide mb-0.5">Document B</p>
            <p class="text-sm font-medium text-gray-800 truncate">{{ $b->title }}</p>
            <p class="text-xs text-gray-500">ID #{{ $b->id }}</p>
        </div>
    </div>

    {{-- Metadata comparison --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700">Metadata</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wide">
                        <th class="px-4 py-2.5 text-left w-36">Field</th>
                        <th class="px-4 py-2.5 text-left">Document A</th>
                        <th class="px-4 py-2.5 text-left">Document B</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @foreach($metaDiff as $row)
                @php $different = $row['a'] !== $row['b']; @endphp
                <tr class="{{ $different ? 'bg-amber-50' : '' }}">
                    <td class="px-4 py-2.5 text-xs font-medium text-gray-500 whitespace-nowrap align-top">
                        {{ $row['label'] }}
                        @if($different)
                        <span class="ml-1 text-amber-500">&#9679;</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 text-gray-800 align-top">
                        <span class="{{ $different ? 'bg-red-50 text-red-800 rounded px-1' : '' }}">
                            {{ $row['a'] }}
                        </span>
                    </td>
                    <td class="px-4 py-2.5 text-gray-800 align-top">
                        <span class="{{ $different ? 'bg-green-50 text-green-800 rounded px-1' : '' }}">
                            {{ $row['b'] }}
                        </span>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Content diff --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Extracted Text Content</h2>
            @php
                $addedCount   = collect($textDiff)->where('type', 'added')->count();
                $removedCount = collect($textDiff)->where('type', 'removed')->count();
            @endphp
            <span class="text-xs text-gray-400">
                <span class="text-green-600 font-medium">+{{ $addedCount }}</span>
                /
                <span class="text-red-600 font-medium">-{{ $removedCount }}</span>
                lines
            </span>
        </div>

        @if(empty($textDiff))
        <div class="px-5 py-8 text-center text-sm text-gray-400">
            No extracted text available for either document.
        </div>
        @else
        <div class="overflow-x-auto" x-data="{ collapsed: true }" x-cloak>

            {{-- Show/collapse toggle when diff is long --}}
            @if(count($textDiff) > 60)
            <div class="px-4 py-2 border-b border-gray-100 bg-gray-50 flex justify-end">
                <button @click="collapsed = !collapsed"
                        class="text-xs text-blue-600 hover:underline"
                        x-text="collapsed ? 'Show all {{ count($textDiff) }} lines' : 'Collapse to changed lines'">
                </button>
            </div>
            @endif

            <div class="font-mono text-xs leading-5 overflow-auto" style="max-height: 65vh;">
                @foreach($textDiff as $i => $line)
                @php
                    $isChange = $line['type'] !== 'context';
                    $show     = $isChange;

                    // Show 3 context lines around changes
                    if (!$show) {
                        foreach (range(max(0, $i-3), min(count($textDiff)-1, $i+3)) as $j) {
                            if (isset($textDiff[$j]) && $textDiff[$j]['type'] !== 'context') {
                                $show = true; break;
                            }
                        }
                    }
                @endphp
                <div @if(!$isChange)x-show="!collapsed || {{ $show ? 'true' : 'false' }}"@endif
                     class="flex items-start px-4 py-0.5 border-b border-gray-50
                     {{ $line['type'] === 'added'   ? 'bg-green-50 border-l-2 border-l-green-400' : '' }}
                     {{ $line['type'] === 'removed' ? 'bg-red-50 border-l-2 border-l-red-400'     : '' }}
                     {{ $line['type'] === 'context' ? 'text-gray-500'                             : '' }}">
                    <span class="w-5 flex-shrink-0 select-none text-gray-300 text-right mr-3">
                        {{ $line['type'] === 'added' ? '+' : ($line['type'] === 'removed' ? '-' : ' ') }}
                    </span>
                    <span class="whitespace-pre-wrap break-words min-w-0">{{ $line['text'] === '' ? ' ' : $line['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3 pb-2">
        <a href="{{ route('admin.documents.edit', $a) }}"
           class="px-4 py-2 border border-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
            Edit Document A
        </a>
        <a href="{{ route('admin.documents.edit', $b) }}"
           class="px-4 py-2 border border-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
            Edit Document B
        </a>
        <a href="{{ route('admin.compare.show', [$b->id, $a->id]) }}"
           class="ml-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
            &#8646; Swap
        </a>
    </div>

</div>
@endsection
