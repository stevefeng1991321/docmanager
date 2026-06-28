@extends('layouts.admin')
@section('title', 'Work Reports')

@section('content')
<div class="space-y-5">

    @include('admin.work-reports._tabs', ['active' => 'reports'])

    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or notes…"
               class="w-52 border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <select name="employee_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('admin.work_reports.all_employees') }}</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
            @endforeach
        </select>
        <select name="department_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('admin.employees.all_departments') }}</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
            @endforeach
        </select>
        <select name="manager_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Managers</option>
            @foreach($managers as $mgr)
                <option value="{{ $mgr->id }}" @selected(request('manager_id') == $mgr->id)>{{ $mgr->full_name }}</option>
            @endforeach
        </select>
        <select name="project_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Projects</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}" @selected(request('project_id') == $project->id)>{{ $project->name }}</option>
            @endforeach
        </select>
        <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('admin.work_reports.all_types') }}</option>
            @foreach(['daily','weekly','monthly'] as $t)
                <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('common.all_status') }}</option>
            @foreach(['draft','submitted','under_review','approved','rejected'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('common.filter') }}</button>
        @if(request()->hasAny(['search','employee_id','department_id','manager_id','project_id','type','status','date_from','date_to']))
            <a href="{{ route('admin.work-reports.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">{{ __('common.clear') }}</a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('common.title') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.work_reports.col_employee') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('common.department') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.work_reports.col_type') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('common.date') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.work_reports.col_status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($reports as $report)
                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.work-reports.show', $report) }}'">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $report->title }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $report->employee->full_name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $report->employee->department?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 capitalize">{{ $report->type }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $report->report_date->format('M j, Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize
                            {{ match($report->status) {
                                'draft' => 'bg-gray-100 text-gray-600',
                                'submitted' => 'bg-blue-100 text-blue-700',
                                'under_review' => 'bg-amber-100 text-amber-700',
                                'approved' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700',
                            } }}">
                            {{ str_replace('_', ' ', $report->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">{{ __('admin.work_reports.no_reports') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $reports->links('vendor.pagination.admin-compact') }}
        </div>
    </div>
</div>
@endsection
