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

    {{-- Calendar --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Attendance Records — {{ $start->format('F Y') }}</h3>
        </div>

        {{-- Day headers --}}
        <div class="grid grid-cols-7 border-b border-gray-100">
            @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
            <div class="py-2 text-center text-xs font-semibold {{ in_array($day, ['Sat','Sun']) ? 'text-gray-400' : 'text-gray-500' }}">
                {{ $day }}
            </div>
            @endforeach
        </div>

        @php
            $colors = [
                'present'  => ['bg' => 'bg-green-500',  'light' => 'bg-green-50  text-green-700  border-green-200'],
                'absent'   => ['bg' => 'bg-red-500',    'light' => 'bg-red-50    text-red-700    border-red-200'],
                'late'     => ['bg' => 'bg-yellow-400', 'light' => 'bg-yellow-50 text-yellow-700 border-yellow-200'],
                'on_leave' => ['bg' => 'bg-blue-500',   'light' => 'bg-blue-50   text-blue-700   border-blue-200'],
                'holiday'  => ['bg' => 'bg-purple-500', 'light' => 'bg-purple-50 text-purple-700 border-purple-200'],
                'half_day' => ['bg' => 'bg-orange-400', 'light' => 'bg-orange-50 text-orange-700 border-orange-200'],
            ];

            // Pad to Monday start
            $firstDow = $start->copy()->dayOfWeekIso; // 1=Mon … 7=Sun
            $calStart = $start->copy()->subDays($firstDow - 1);
            $lastDow  = $end->copy()->dayOfWeekIso;
            $calEnd   = $end->copy()->addDays(7 - $lastDow);
            $cur      = $calStart->copy();
        @endphp

        <div class="grid grid-cols-7">
            @while($cur <= $calEnd)
            @php
                $dateStr  = $cur->toDateString();
                $rec      = $records->get($dateStr);
                $inMonth  = $cur->month === $start->month;
                $isWknd   = $cur->isWeekend();
                $isToday  = $cur->isToday();
                $isFuture = $cur->isFuture();
                $cur->addDay();
            @endphp
            <div class="min-h-[80px] p-1.5 border-b border-r border-gray-100 {{ !$inMonth ? 'bg-gray-50/50' : '' }} {{ $isToday ? 'bg-blue-50' : '' }}">
                {{-- Date number --}}
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-medium w-6 h-6 flex items-center justify-center rounded-full
                        {{ $isToday ? 'bg-blue-600 text-white' : ($isWknd && $inMonth ? 'text-gray-400' : ($inMonth ? 'text-gray-700' : 'text-gray-300')) }}">
                        {{ \Carbon\Carbon::parse($dateStr)->day }}
                    </span>
                </div>

                {{-- Status badge --}}
                @if($inMonth && !$isFuture)
                    @if($rec)
                    <div class="rounded-md border px-1.5 py-0.5 text-center {{ $colors[$rec->status]['light'] ?? 'bg-gray-50 text-gray-500 border-gray-200' }}">
                        <div class="text-[10px] font-semibold leading-tight">{{ $rec->status_label }}</div>
                        @if($rec->check_in_time)
                        <div class="text-[9px] opacity-70 leading-tight">{{ $rec->check_in_time }}{{ $rec->check_out_time ? ' – '.$rec->check_out_time : '' }}</div>
                        @endif
                    </div>
                    @elseif(!$isWknd)
                    <div class="rounded-md border border-gray-200 bg-gray-50 px-1.5 py-0.5 text-center">
                        <div class="text-[10px] text-gray-400 leading-tight">No record</div>
                    </div>
                    @endif
                @endif
            </div>
            @endwhile
        </div>

        {{-- Legend --}}
        <div class="px-4 py-3 border-t border-gray-100 flex flex-wrap gap-3">
            @foreach(['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late', 'on_leave' => 'On Leave', 'holiday' => 'Holiday', 'half_day' => 'Half Day'] as $key => $label)
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-sm {{ $colors[$key]['bg'] }}"></span>
                <span class="text-xs text-gray-500">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

@endif
@endsection
