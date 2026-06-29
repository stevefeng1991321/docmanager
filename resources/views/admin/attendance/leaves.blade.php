@extends('layouts.admin')
@section('title', 'Leave Requests')

@section('content')
<div x-data="{ rejectLeaveId: null, rejectReason: '', showReject: false }">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Leave Requests</h2>
            <p class="text-sm text-gray-500 mt-0.5">Review and action employee leave applications</p>
        </div>
        <a href="{{ route('admin.attendance.index') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            ← Attendance
        </a>
    </div>

    {{-- Status tabs --}}
    @php
        $tabs = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'];
    @endphp
    <div class="flex gap-1 mb-5 border-b border-gray-200">
        @foreach($tabs as $val => $label)
        <a href="{{ route('admin.attendance.leaves.index', ['status' => $val]) }}"
           class="px-4 py-2 text-sm font-medium transition border-b-2 -mb-px
               {{ $status === $val ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            {{ $label }}
            @if(isset($counts[$val]) && $counts[$val])
            <span class="ml-1.5 text-xs {{ $status === $val ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }} px-1.5 py-0.5 rounded-full">
                {{ $counts[$val] }}
            </span>
            @endif
        </a>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">{{ __('admin.attendance.col_employee') }}</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">{{ __('common.type') }}</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">{{ __('admin.work_reports.col_period') }}</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden sm:table-cell">Days</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">{{ __('admin.attendance.col_status') }}</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden lg:table-cell">Applied</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($leaves as $leave)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $leave->employee->full_name }}</div>
                        <div class="text-xs text-gray-400">{{ $leave->employee->department?->name ?? '—' }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $leave->leave_type_label }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $leave->start_date->format('M d') }} – {{ $leave->end_date->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $leave->days_count }}</td>
                    <td class="px-4 py-3">
                        @include('partials.leave-colors')
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $sc[$leave->status] }}">
                            {{ ucfirst($leave->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs hidden lg:table-cell">{{ $leave->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($leave->isPending())
                            <form method="POST" action="{{ route('admin.attendance.leaves.approve', $leave) }}">
                                @csrf
                                <button class="text-xs font-semibold text-green-700 hover:text-green-900 transition">{{ __('common.approve') }}</button>
                            </form>
                            <button @click="rejectLeaveId = {{ $leave->id }}; rejectReason = ''; showReject = true"
                                    class="text-xs font-semibold text-red-600 hover:text-red-800 transition">{{ __('common.reject') }}</button>
                            @endif
                            <form method="POST" action="{{ route('admin.attendance.leaves.destroy', $leave) }}"
                                  onsubmit="return confirm('{{ __('admin.attendance.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-gray-400 hover:text-red-600 transition">{{ __('common.delete') }}</button>
                            </form>
                        </div>
                        @if($leave->rejection_reason)
                        <p class="text-xs text-red-500 mt-1 max-w-xs text-right">{{ $leave->rejection_reason }}</p>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-400">{{ __('admin.attendance.no_records') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($leaves->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $leaves->links() }}</div>
        @endif
    </div>

    {{-- Reject modal --}}
    <div x-show="showReject" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @keydown.escape.window="showReject = false">
        <div @click.outside="showReject = false"
             class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800 text-sm">Reject Leave Request</h3>
                <button @click="showReject = false" class="text-gray-400 hover:text-gray-600 text-lg leading-none">&times;</button>
            </div>
            <form method="POST" :action="`{{ url('admin/attendance/leaves') }}/` + rejectLeaveId + '/reject'" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Rejection Reason <span class="text-red-500">*</span></label>
                    <textarea name="rejection_reason" x-model="rejectReason" rows="3" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"
                              placeholder="Provide a reason for rejection…"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showReject = false"
                            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                        {{ __('common.reject') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
