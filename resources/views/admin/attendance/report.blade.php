@extends('layouts.admin')
@section('title', 'Attendance Report')

@section('content')

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Attendance Report</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $start->format('F Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.attendance.export', ['month' => $month, 'year' => $year, 'employee_id' => $employeeId, 'department_id' => $deptId]) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV
            </a>
            <a href="{{ route('admin.attendance.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                ← Daily View
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.attendance.report') }}"
          class="bg-white border border-gray-200 rounded-xl p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Month</label>
            <select name="month" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" @selected($m == $month)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Year</label>
            <select name="year" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
            <select name="department_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}" @selected($dept->id == $deptId)>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Employee</label>
            <select name="employee_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">All Employees</option>
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}" @selected($emp->id == $employeeId)>{{ $emp->full_name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="px-4 py-1.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
            Apply
        </button>
    </form>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        @php
            $statCards = [
                ['label' => 'Working Days', 'value' => $workingDays,                   'color' => 'gray'],
                ['label' => 'Present',       'value' => $stats['present'] ?? 0,        'color' => 'green'],
                ['label' => 'Absent',        'value' => $stats['absent'] ?? 0,         'color' => 'red'],
                ['label' => 'Late',          'value' => $stats['late'] ?? 0,           'color' => 'yellow'],
                ['label' => 'On Leave',      'value' => $stats['on_leave'] ?? 0,       'color' => 'blue'],
                ['label' => 'Holiday',       'value' => $stats['holiday'] ?? 0,        'color' => 'purple'],
            ];
            $colorMap = [
                'gray'   => 'bg-gray-50 border-gray-200 text-gray-700',
                'green'  => 'bg-green-50 border-green-200 text-green-700',
                'red'    => 'bg-red-50 border-red-200 text-red-700',
                'yellow' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
                'blue'   => 'bg-blue-50 border-blue-200 text-blue-700',
                'purple' => 'bg-purple-50 border-purple-200 text-purple-700',
            ];
        @endphp
        @foreach($statCards as $card)
        <div class="rounded-xl border p-3 text-center {{ $colorMap[$card['color']] }}">
            <div class="text-2xl font-bold">{{ $card['value'] }}</div>
            <div class="text-xs font-medium mt-0.5">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Date</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Employee</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden sm:table-cell">Department</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden md:table-cell">Check In</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden md:table-cell">Check Out</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden lg:table-cell">Duration</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($records as $rec)
                @php
                    $colors = [
                        'present'  => 'bg-green-100 text-green-700',
                        'absent'   => 'bg-red-100 text-red-700',
                        'late'     => 'bg-yellow-100 text-yellow-700',
                        'on_leave' => 'bg-blue-100 text-blue-700',
                        'holiday'  => 'bg-purple-100 text-purple-700',
                        'half_day' => 'bg-orange-100 text-orange-700',
                    ];
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-700">{{ $rec->date->format('M d, Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $rec->employee->full_name }}</div>
                        <div class="text-xs text-gray-400">{{ $rec->employee->employee_code }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">{{ $rec->employee->department?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$rec->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $rec->status_label }}
                        </span>
                        @if($rec->status === 'late' && $rec->late_minutes)
                            <span class="text-xs text-gray-400 ml-1">+{{ $rec->late_minutes }}m</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $rec->check_in_time ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $rec->check_out_time ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 hidden lg:table-cell">{{ $rec->work_duration ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-400">No records found for this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($records->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $records->links() }}
        </div>
        @endif
    </div>

@endsection
