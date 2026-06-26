<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\WorkReport;
use Illuminate\Http\Request;

class WorkReportAnalyticsController extends Controller
{
    public function index()
    {
        $statusCounts = WorkReport::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $typeCounts = WorkReport::query()
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $departmentStats = WorkReport::query()
            ->join('employees', 'employees.id', '=', 'work_reports.employee_id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.department_id')
            ->selectRaw('COALESCE(departments.name, "Unassigned") as department_name, COUNT(*) as report_count, SUM(work_reports.work_hours) as total_hours')
            ->groupBy('department_name')
            ->orderByDesc('report_count')
            ->get();

        $projectStats = Project::query()
            ->withCount('workReports')
            ->withAvg('workReports', 'overall_progress')
            ->orderByDesc('work_reports_count')
            ->get();

        $topContributors = WorkReport::query()
            ->join('employees', 'employees.id', '=', 'work_reports.employee_id')
            ->selectRaw('employees.full_name, COUNT(*) as report_count, SUM(work_reports.work_hours) as total_hours, AVG(work_reports.overall_progress) as avg_progress')
            ->groupBy('employees.full_name')
            ->orderByDesc('report_count')
            ->limit(10)
            ->get();

        $submittedToday = WorkReport::whereDate('submitted_at', today())->count();
        $submittedThisWeek = WorkReport::where('submitted_at', '>=', now()->startOfWeek())->count();
        $submittedThisMonth = WorkReport::where('submitted_at', '>=', now()->startOfMonth())->count();

        return view('admin.work-report-analytics.index', compact(
            'statusCounts', 'typeCounts', 'departmentStats', 'projectStats', 'topContributors',
            'submittedToday', 'submittedThisWeek', 'submittedThisMonth'
        ));
    }

    public function export(Request $request)
    {
        $reports = WorkReport::with(['employee.department', 'project'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->orderByDesc('report_date')
            ->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="work-reports.csv"'];

        $callback = function () use ($reports) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Title', 'Employee', 'Department', 'Type', 'Date', 'Project', 'Status', 'Work Hours', 'Progress %']);
            foreach ($reports as $report) {
                fputcsv($out, [
                    $report->title,
                    $report->employee->full_name,
                    $report->employee->department?->name ?? '—',
                    $report->type,
                    $report->report_date->format('Y-m-d'),
                    $report->project?->name ?? '—',
                    $report->status,
                    $report->work_hours ?? '',
                    $report->overall_progress ?? '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
