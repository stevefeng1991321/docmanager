@extends('layouts.admin')
@section('title', 'Attendance')

@section('content')
<div x-data="attendancePage()" x-init="init()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">{{ __('admin.attendance.heading') }}</h2>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <form method="GET" action="{{ route('admin.attendance.index') }}" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $date }}"
                       class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       onchange="this.form.submit()">
            </form>
            <button @click="showBulk = true"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                {{ __('admin.attendance.add_record') }}
            </button>
            <a href="{{ route('admin.attendance.export', ['date' => $date]) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                {{ __('admin.attendance.export') }}
            </a>
            <a href="{{ route('admin.attendance.report') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                {{ __('admin.attendance.summary_heading') }}
            </a>
            <a href="{{ route('admin.attendance.leaves.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Leave Requests
            </a>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        @php
            $cards = [
                ['label' => 'Total',      'value' => $summary['total'],      'color' => 'gray'],
                ['label' => 'Present',    'value' => $summary['present'],    'color' => 'green'],
                ['label' => 'Absent',     'value' => $summary['absent'],     'color' => 'red'],
                ['label' => 'Late',       'value' => $summary['late'],       'color' => 'yellow'],
                ['label' => 'On Leave',   'value' => $summary['on_leave'],   'color' => 'blue'],
                ['label' => 'Not Marked', 'value' => $summary['not_marked'], 'color' => 'orange'],
            ];
            $colorMap = [
                'gray'   => 'bg-gray-50 border-gray-200 text-gray-700',
                'green'  => 'bg-green-50 border-green-200 text-green-700',
                'red'    => 'bg-red-50 border-red-200 text-red-700',
                'yellow' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
                'blue'   => 'bg-blue-50 border-blue-200 text-blue-700',
                'orange' => 'bg-orange-50 border-orange-200 text-orange-700',
            ];
        @endphp
        @foreach($cards as $card)
        <div class="rounded-xl border p-3 text-center {{ $colorMap[$card['color']] }}">
            <div class="text-2xl font-bold">{{ $card['value'] }}</div>
            <div class="text-xs font-medium mt-0.5">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Employee table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">{{ __('admin.attendance.col_employee') }}</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden sm:table-cell">{{ __('common.department') }}</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">{{ __('admin.attendance.col_status') }}</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden md:table-cell">{{ __('admin.attendance.col_check_in') }}</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden md:table-cell">{{ __('admin.attendance.col_check_out') }}</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">{{ __('common.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($employees as $emp)
                @php
                    $att    = $emp->today_attendance;
                    $status = $att?->status?->value ?? 'not_marked';
                    $colors = [
                        'present'    => 'bg-green-100 text-green-700',
                        'absent'     => 'bg-red-100 text-red-700',
                        'late'       => 'bg-yellow-100 text-yellow-700',
                        'on_leave'   => 'bg-blue-100 text-blue-700',
                        'holiday'    => 'bg-purple-100 text-purple-700',
                        'half_day'   => 'bg-orange-100 text-orange-700',
                        'not_marked' => 'bg-gray-100 text-gray-500',
                    ];
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($emp->full_name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-800">{{ $emp->full_name }}</div>
                                <div class="text-xs text-gray-400">{{ $emp->employee_code }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">{{ $emp->department?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$status] }}">
                            {{ ucwords(str_replace('_', ' ', $status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $att?->check_in_time ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $att?->check_out_time ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <button @click="openMark({{ $emp->id }}, '{{ addslashes($emp->full_name) }}', '{{ $att?->status?->value ?? '' }}', '{{ $att?->check_in_time ?? '' }}', '{{ $att?->check_out_time ?? '' }}', {{ $att?->late_minutes ?? 0 }}, '{{ addslashes($att?->notes ?? '') }}')"
                                class="text-xs text-blue-600 hover:text-blue-800 font-medium transition">
                            {{ $att ? 'Edit' : 'Mark' }}
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">No active employees found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Bulk mark modal --}}
    <div x-show="showBulk" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @keydown.escape.window="showBulk = false">
        <div @click.outside="showBulk = false"
             class="bg-white rounded-xl shadow-xl w-full max-w-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800 text-sm">{{ __('admin.attendance.manual_entry') }}</h3>
                <button @click="showBulk = false" class="text-gray-400 hover:text-gray-600 transition text-lg leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.attendance.bulk-mark') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                <p class="text-xs text-gray-500">
                    Date: <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($date)->format('l, M d Y') }}</span>
                </p>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">{{ __('admin.attendance.status_label') }} <span class="text-red-500">*</span></label>
                    <div class="space-y-1.5">
                        @foreach(['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late', 'on_leave' => 'On Leave', 'holiday' => 'Holiday'] as $val => $label)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="{{ $val }}" x-model="bulkStatus"
                                   class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                    Only employees <strong>not yet marked</strong> for this date will be affected.
                </p>

                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" @click="showBulk = false"
                            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                        {{ __('common.apply') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Mark attendance modal --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @keydown.escape.window="showModal = false">
        <div @click.outside="showModal = false"
             class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800 text-sm">{{ __('admin.attendance.manual_entry') }} — <span x-text="modalEmployee"></span></h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition text-lg leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.attendance.mark') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="employee_id" :value="modalEmployeeId">
                <input type="hidden" name="date" value="{{ $date }}">

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">{{ __('admin.attendance.status_label') }} <span class="text-red-500">*</span></label>
                    <div class="space-y-1.5">
                        @foreach(['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late', 'on_leave' => 'On Leave', 'holiday' => 'Holiday', 'half_day' => 'Half Day'] as $val => $label)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="{{ $val }}" x-model="modalStatus"
                                   class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('admin.attendance.col_check_in') }}</label>
                        <input type="time" name="check_in_time" x-model="modalCheckIn"
                               class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('admin.attendance.col_check_out') }}</label>
                        <input type="time" name="check_out_time" x-model="modalCheckOut"
                               class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div x-show="modalStatus === 'late'" x-cloak>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('admin.attendance.col_hours') }}</label>
                    <input type="number" name="late_minutes" x-model="modalLateMinutes" min="0" max="480"
                           class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('admin.attendance.note_label') }}</label>
                    <textarea name="notes" x-model="modalNotes" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"
                              placeholder="Optional notes…"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" @click="showModal = false"
                            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                        {{ __('common.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function attendancePage() {
    return {
        showModal: false,
        modalEmployeeId: null,
        modalEmployee: '',
        modalStatus: 'present',
        modalCheckIn: '',
        modalCheckOut: '',
        modalLateMinutes: 0,
        modalNotes: '',
        showBulk: false,
        bulkStatus: 'present',

        init() {},

        openMark(id, name, status, checkIn, checkOut, lateMinutes, notes) {
            this.modalEmployeeId  = id;
            this.modalEmployee    = name;
            this.modalStatus      = status || 'present';
            this.modalCheckIn     = checkIn || '';
            this.modalCheckOut    = checkOut || '';
            this.modalLateMinutes = lateMinutes || 0;
            this.modalNotes       = notes || '';
            this.showModal        = true;
        },
    };
}
</script>
@endpush
