@extends('layouts.app')
@section('title', 'Work Reports')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <h1 class="text-xl font-bold text-gray-800">Work Reports</h1>
        <a href="{{ route('work-reports.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            + New Report
        </a>
    </div>

    @if($isManager)
    <div class="border-b border-gray-200 flex gap-1">
        <a href="{{ route('work-reports.index', array_merge(request()->except('tab', 'page'), ['tab' => 'mine'])) }}"
           class="px-3.5 py-2 text-sm font-medium border-b-2 transition {{ $tab === 'mine' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            My Reports
        </a>
        <a href="{{ route('work-reports.index', array_merge(request()->except('tab', 'page'), ['tab' => 'team'])) }}"
           class="px-3.5 py-2 text-sm font-medium border-b-2 transition {{ $tab === 'team' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Team
        </a>
    </div>
    @endif

    <form method="GET" class="flex flex-wrap gap-2">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or notes…"
               class="w-56 border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Types</option>
            @foreach(['daily','weekly','monthly'] as $t)
                <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Status</option>
            @foreach(['draft','submitted','under_review','approved','rejected'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
        <select name="project_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Projects</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}" @selected(request('project_id') == $project->id)>{{ $project->name }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">Filter</button>
        @if(request()->hasAny(['search','type','status','project_id','date_from','date_to']))
            <a href="{{ route('work-reports.index', ['tab' => $tab]) }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Title</th>
                    @if($tab === 'team')
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Employee</th>
                    @endif
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Project</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($reports as $report)
                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('work-reports.show', $report) }}'">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $report->title }}</td>
                    @if($tab === 'team')
                    <td class="px-4 py-3 text-gray-600">{{ $report->employee->full_name }}</td>
                    @endif
                    <td class="px-4 py-3 text-gray-600 capitalize">{{ $report->type }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $report->report_date->format('M j, Y') }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $report->project?->name ?? '—' }}</td>
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
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">
                    @if(!$employee)
                        You don't have an employee profile linked to your account yet — contact an administrator.
                    @else
                        No work reports found.
                    @endif
                </td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $reports->links('vendor.pagination.admin-compact') }}
        </div>
    </div>
</div>
@endsection
