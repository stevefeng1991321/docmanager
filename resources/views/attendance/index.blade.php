@extends('layouts.app')
@section('title', 'My Attendance')

@section('content')

@if(!$employee)
<div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
    <p class="text-yellow-800 font-medium">No employee profile is linked to your account.</p>
    <p class="text-yellow-600 text-sm mt-1">Please contact your HR administrator.</p>
</div>
@else

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">My Attendance</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $start->format('F Y') }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Month navigation --}}
            @php
                $prev = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
                $next = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
            @endphp
            <a href="{{ route('attendance.index', ['month' => $prev->month, 'year' => $prev->year]) }}"
               class="p-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <span class="text-sm font-medium text-gray-700">{{ $start->format('M Y') }}</span>
            <a href="{{ route('attendance.index', ['month' => $next->month, 'year' => $next->year]) }}"
               class="p-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <a href="{{ route('attendance.leaves.index') }}"
               class="ml-2 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                My Leaves
            </a>
        </div>
    </div>

    {{-- Check-in/out card --}}
    @php $isToday = $month == today()->month && $year == today()->year; @endphp
    @if($isToday)
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-gray-800">Today — {{ today()->format('l, M d Y') }}</p>
            @if($today)
                <p class="text-xs text-gray-500 mt-0.5">
                    Status:
                    @php
                        $sc = ['present'=>'text-green-600','absent'=>'text-red-600','late'=>'text-yellow-600','on_leave'=>'text-blue-600','holiday'=>'text-purple-600','half_day'=>'text-orange-600'];
                    @endphp
                    <span class="font-medium {{ $sc[$today->status] ?? 'text-gray-600' }}">{{ $today->status_label }}</span>
                    @if($today->check_in_time)  · In: {{ $today->check_in_time }} @endif
                    @if($today->check_out_time) · Out: {{ $today->check_out_time }} @endif
                    @if($today->work_duration)  · {{ $today->work_duration }} @endif
                </p>
            @else
                <p class="text-xs text-gray-400 mt-0.5">Not yet marked for today</p>
            @endif
        </div>
        <div class="flex gap-2">
            @if(!$today || !$today->check_in_time)
            <form method="POST" action="{{ route('attendance.check-in') }}">
                @csrf
                <button class="px-4 py-2 text-sm font-semibold bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    Check In
                </button>
            </form>
            @elseif(!$today->check_out_time)
            <form method="POST" action="{{ route('attendance.check-out') }}">
                @csrf
                <button class="px-4 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    Check Out
                </button>
            </form>
            @else
            <span class="px-4 py-2 text-sm text-gray-500 bg-gray-100 rounded-lg">Completed for today</span>
            @endif
        </div>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        @php
            $statCards = [
                ['label' => 'Present',  'value' => $stats['present'],  'color' => 'green'],
                ['label' => 'Absent',   'value' => $stats['absent'],   'color' => 'red'],
                ['label' => 'Late',     'value' => $stats['late'],     'color' => 'yellow'],
                ['label' => 'On Leave', 'value' => $stats['on_leave'], 'color' => 'blue'],
            ];
            $colorMap = [
                'green'  => 'bg-green-50 border-green-200 text-green-700',
                'red'    => 'bg-red-50 border-red-200 text-red-700',
                'yellow' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
                'blue'   => 'bg-blue-50 border-blue-200 text-blue-700',
            ];
        @endphp
        @foreach($statCards as $card)
        <div class="rounded-xl border p-4 text-center {{ $colorMap[$card['color']] }}">
            <div class="text-2xl font-bold">{{ $card['value'] }}</div>
            <div class="text-xs font-medium mt-0.5">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Attendance list --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Attendance Records — {{ $start->format('F Y') }}</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Date</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Day</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden sm:table-cell">Check In</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden sm:table-cell">Check Out</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden md:table-cell">Duration</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php
                    $current = $start->copy();
                    $colors = [
                        'present'  => 'bg-green-100 text-green-700',
                        'absent'   => 'bg-red-100 text-red-700',
                        'late'     => 'bg-yellow-100 text-yellow-700',
                        'on_leave' => 'bg-blue-100 text-blue-700',
                        'holiday'  => 'bg-purple-100 text-purple-700',
                        'half_day' => 'bg-orange-100 text-orange-700',
                    ];
                @endphp
                @while($current <= $end)
                @php
                    $dateStr = $current->toDateString();
                    $rec     = $records->get($dateStr);
                    $isWknd  = $current->isWeekend();
                    $isFuture = $current->isFuture();
                @endphp
                <tr class="{{ $isWknd ? 'bg-gray-50/50' : '' }} {{ $current->isToday() ? 'bg-blue-50/30' : '' }}">
                    <td class="px-4 py-2.5 {{ $isWknd ? 'text-gray-400' : 'text-gray-700' }}">{{ $current->format('M d') }}</td>
                    <td class="px-4 py-2.5 {{ $isWknd ? 'text-gray-400' : 'text-gray-500' }} text-xs">{{ $current->format('D') }}</td>
                    <td class="px-4 py-2.5">
                        @if($isWknd)
                            <span class="text-xs text-gray-400">Weekend</span>
                        @elseif($isFuture)
                            <span class="text-xs text-gray-400">—</span>
                        @elseif($rec)
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$rec->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $rec->status_label }}
                            </span>
                        @else
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Not Marked</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 text-gray-500 text-xs hidden sm:table-cell">{{ $rec?->check_in_time ?? '—' }}</td>
                    <td class="px-4 py-2.5 text-gray-500 text-xs hidden sm:table-cell">{{ $rec?->check_out_time ?? '—' }}</td>
                    <td class="px-4 py-2.5 text-gray-500 text-xs hidden md:table-cell">{{ $rec?->work_duration ?? '—' }}</td>
                </tr>
                @php $current->addDay() @endphp
                @endwhile
            </tbody>
        </table>
    </div>

@endif
@endsection
