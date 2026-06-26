@extends('layouts.admin')
@section('title', 'Employee Reports')

@section('content')
<div class="space-y-5">

    @include('admin.employees._tabs', ['active' => 'reports'])

    <div class="flex items-center justify-between">
        <h1 class="text-lg font-bold text-gray-800">Employee Reports</h1>
        <a href="{{ route('admin.employee-reports.export') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
            ⬇ Export Directory (CSV)
        </a>
    </div>

    {{-- Active vs Inactive --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Active vs. Inactive</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'resigned' => 'Resigned', 'terminated' => 'Terminated'] as $key => $label)
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">{{ $label }}</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($statusCounts[$key] ?? 0) }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Distribution by Department --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Distribution by Department</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Department</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Employees</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($byDepartment as $dept)
                <tr>
                    <td class="px-4 py-2.5 text-gray-700">{{ $dept->name }}</td>
                    <td class="px-4 py-2.5 text-gray-500">{{ $dept->employees_count }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="px-4 py-6 text-center text-gray-400">No departments yet.</td></tr>
                @endforelse
                @if($unassignedCount > 0)
                <tr class="bg-gray-50/50">
                    <td class="px-4 py-2.5 text-gray-500 italic">No department</td>
                    <td class="px-4 py-2.5 text-gray-500">{{ $unassignedCount }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Department Employee List --}}
    <div x-data="{ open: @js($rosterByDepartment->keys()->mapWithKeys(fn($k) => [$k => true])) }"
         class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Department Employee List</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($rosterByDepartment as $deptName => $deptEmployees)
            @php $deptKey = \Illuminate\Support\Js::from($deptName); @endphp
            <div>
                <button type="button" @click="open[{{ $deptKey }}] = !open[{{ $deptKey }}]"
                        class="w-full flex items-center justify-between px-5 py-2.5 hover:bg-gray-50 text-left">
                    <span class="text-sm font-medium text-gray-700">{{ $deptName }}</span>
                    <span class="text-xs text-gray-400">{{ $deptEmployees->count() }} employee(s)</span>
                </button>
                <div x-show="open[{{ $deptKey }}]" x-cloak>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-50">
                            @foreach($deptEmployees as $employee)
                            <tr class="bg-gray-50/40">
                                <td class="px-5 py-2 pl-10 text-gray-500 font-mono text-xs w-28">{{ $employee->employee_code }}</td>
                                <td class="px-4 py-2"><a href="{{ route('admin.employees.show', $employee) }}" class="text-blue-600 hover:underline">{{ $employee->full_name }}</a></td>
                                <td class="px-4 py-2 text-gray-500">{{ $employee->position?->title ?? '—' }}</td>
                                <td class="px-4 py-2 text-gray-400 capitalize text-xs">{{ $employee->employment_status }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @empty
            <p class="px-5 py-6 text-center text-gray-400 text-sm">No employees yet.</p>
            @endforelse
        </div>
    </div>

    {{-- New Hires (last 30 days) --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">New Hires <span class="text-gray-400 font-normal">(last 30 days)</span></h2>
        </div>
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Department</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Position</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($newHires as $employee)
                <tr>
                    <td class="px-4 py-2.5"><a href="{{ route('admin.employees.show', $employee) }}" class="text-blue-600 hover:underline">{{ $employee->full_name }}</a></td>
                    <td class="px-4 py-2.5 text-gray-600">{{ $employee->department?->name ?? '—' }}</td>
                    <td class="px-4 py-2.5 text-gray-600">{{ $employee->position?->title ?? '—' }}</td>
                    <td class="px-4 py-2.5 text-gray-500">{{ $employee->date_of_joining?->format('M j, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No new hires in the last 30 days.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Employee Directory --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Employee Directory <span class="text-gray-400 font-normal">({{ $employees->count() }})</span></h2>
        </div>
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Department</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Position</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($employees as $employee)
                <tr>
                    <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $employee->employee_code }}</td>
                    <td class="px-4 py-2.5"><a href="{{ route('admin.employees.show', $employee) }}" class="text-blue-600 hover:underline">{{ $employee->full_name }}</a></td>
                    <td class="px-4 py-2.5 text-gray-600">{{ $employee->department?->name ?? '—' }}</td>
                    <td class="px-4 py-2.5 text-gray-600">{{ $employee->position?->title ?? '—' }}</td>
                    <td class="px-4 py-2.5">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize
                            {{ match($employee->employment_status) {
                                'active' => 'bg-green-100 text-green-700',
                                'inactive' => 'bg-gray-100 text-gray-600',
                                'resigned' => 'bg-amber-100 text-amber-700',
                                'terminated' => 'bg-red-100 text-red-700',
                            } }}">
                            {{ $employee->employment_status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No employees yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
