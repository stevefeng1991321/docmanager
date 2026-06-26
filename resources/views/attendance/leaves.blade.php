@extends('layouts.app')
@section('title', 'My Leave Requests')

@section('content')
<div x-data="{ showForm: false }">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">My Leave Requests</h1>
            <p class="text-sm text-gray-500 mt-0.5">Submit and track your leave applications</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('attendance.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                ← Attendance
            </a>
            @if($employee)
            <button @click="showForm = !showForm"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                + Apply for Leave
            </button>
            @endif
        </div>
    </div>

    @if(!$employee)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
        <p class="text-yellow-800 font-medium">No employee profile is linked to your account.</p>
    </div>
    @else

    {{-- Apply form (collapsible) --}}
    <div x-show="showForm" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Apply for Leave</h3>
        <form method="POST" action="{{ route('attendance.leaves.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Leave Type <span class="text-red-500">*</span></label>
                    <select name="leave_type" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Select type…</option>
                        <option value="annual">Annual Leave</option>
                        <option value="sick">Sick Leave</option>
                        <option value="personal">Personal Leave</option>
                        <option value="unpaid">Unpaid Leave</option>
                        <option value="maternity">Maternity Leave</option>
                        <option value="paternity">Paternity Leave</option>
                    </select>
                </div>
                <div></div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" required min="{{ today()->toDateString() }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">End Date <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" required min="{{ today()->toDateString() }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Reason</label>
                <textarea name="reason" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"
                          placeholder="Optional reason or notes…"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" @click="showForm = false"
                        class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                    Submit Request
                </button>
            </div>
        </form>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Leaves table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Type</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Period</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden sm:table-cell">Days</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden md:table-cell">Notes</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($leaves as $leave)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $leave->leave_type_label }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $leave->start_date->format('M d') }} – {{ $leave->end_date->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">{{ $leave->days_count }}</td>
                    <td class="px-4 py-3">
                        @include('partials.leave-colors')
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $sc[$leave->status] }}">
                            {{ ucfirst($leave->status) }}
                        </span>
                        @if($leave->rejection_reason)
                        <p class="text-xs text-red-500 mt-1">{{ $leave->rejection_reason }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs max-w-xs truncate hidden md:table-cell">{{ $leave->reason ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        @if($leave->isPending())
                        <form method="POST" action="{{ route('attendance.leaves.destroy', $leave) }}"
                              onsubmit="return confirm('Cancel this leave request?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:text-red-700 font-medium transition">Cancel</button>
                        </form>
                        @else
                        <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-gray-400">No leave requests yet. Click "+ Apply for Leave" to submit one.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($leaves->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $leaves->links() }}</div>
        @endif
    </div>

    @endif
</div>
@endsection
