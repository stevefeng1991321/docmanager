@extends('layouts.admin')
@section('title', 'Work Report Analytics')

@section('content')
<div class="space-y-5">

    @include('admin.work-reports._tabs', ['active' => 'analytics'])

    <div class="flex items-center justify-between">
        <h1 class="text-lg font-bold text-gray-800">Analytics</h1>
        <a href="{{ route('admin.work-report-analytics.export') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
            ⬇ Export (CSV)
        </a>
    </div>

    {{-- Submission stats --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Submission Statistics</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
            <div><p class="text-xs text-gray-400 uppercase tracking-wide">Submitted Today</p><p class="text-xl font-bold text-gray-900">{{ number_format($submittedToday) }}</p></div>
            <div><p class="text-xs text-gray-400 uppercase tracking-wide">This Week</p><p class="text-xl font-bold text-gray-900">{{ number_format($submittedThisWeek) }}</p></div>
            <div><p class="text-xs text-gray-400 uppercase tracking-wide">This Month</p><p class="text-xl font-bold text-gray-900">{{ number_format($submittedThisMonth) }}</p></div>
            <div><p class="text-xs text-gray-400 uppercase tracking-wide">Total Reports</p><p class="text-xl font-bold text-gray-900">{{ number_format($statusCounts->sum()) }}</p></div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 pt-4 border-t border-gray-100">
            @foreach(['draft' => 'Draft', 'submitted' => 'Submitted', 'under_review' => 'Under Review', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
            <div><p class="text-xs text-gray-400 uppercase tracking-wide">{{ $label }}</p><p class="text-lg font-semibold text-gray-700">{{ number_format($statusCounts[$key] ?? 0) }}</p></div>
            @endforeach
        </div>
        <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100 mt-4">
            @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $key => $label)
            <div><p class="text-xs text-gray-400 uppercase tracking-wide">{{ $label }}</p><p class="text-lg font-semibold text-gray-700">{{ number_format($typeCounts[$key] ?? 0) }}</p></div>
            @endforeach
        </div>
    </div>

    {{-- Department activity --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100"><h2 class="text-sm font-semibold text-gray-700">Department Activity</h2></div>
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Department</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Reports</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Total Hours</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($departmentStats as $row)
                <tr>
                    <td class="px-4 py-2.5 text-gray-700">{{ $row->department_name }}</td>
                    <td class="px-4 py-2.5 text-gray-500">{{ $row->report_count }}</td>
                    <td class="px-4 py-2.5 text-gray-500">{{ $row->total_hours ? number_format($row->total_hours, 1) : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">No data yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Project progress --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100"><h2 class="text-sm font-semibold text-gray-700">Project Progress</h2></div>
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Project</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Reports</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Avg. Progress</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($projectStats as $project)
                <tr>
                    <td class="px-4 py-2.5 text-gray-700">{{ $project->name }}</td>
                    <td class="px-4 py-2.5 text-gray-500">{{ $project->work_reports_count }}</td>
                    <td class="px-4 py-2.5 text-gray-500">{{ $project->work_reports_avg_overall_progress ? number_format($project->work_reports_avg_overall_progress, 0).'%' : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">No projects yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Top contributors --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100"><h2 class="text-sm font-semibold text-gray-700">Top Contributors</h2></div>
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Employee</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Reports</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Total Hours</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Avg. Progress</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($topContributors as $row)
                <tr>
                    <td class="px-4 py-2.5 text-gray-700">{{ $row->full_name }}</td>
                    <td class="px-4 py-2.5 text-gray-500">{{ $row->report_count }}</td>
                    <td class="px-4 py-2.5 text-gray-500">{{ $row->total_hours ? number_format($row->total_hours, 1) : '—' }}</td>
                    <td class="px-4 py-2.5 text-gray-500">{{ $row->avg_progress ? number_format($row->avg_progress, 0).'%' : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No data yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
