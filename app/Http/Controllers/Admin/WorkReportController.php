<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;
use App\Models\WorkReport;
use Illuminate\Http\Request;

class WorkReportController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkReport::with(['employee.department', 'employee.manager', 'project'])
            ->when($request->employee_id, fn ($q, $e) => $q->where('employee_id', $e))
            ->when($request->department_id, fn ($q, $d) => $q->whereHas('employee', fn ($w) => $w->where('department_id', $d)))
            ->when($request->manager_id, fn ($q, $m) => $q->whereHas('employee', fn ($w) => $w->where('manager_id', $m)))
            ->when($request->project_id, fn ($q, $p) => $q->where('project_id', $p))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->date_from, fn ($q, $d) => $q->whereDate('report_date', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->whereDate('report_date', '<=', $d))
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('title', 'like', "%{$s}%")
                ->orWhere('tasks_completed', 'like', "%{$s}%")
                ->orWhere('notes', 'like', "%{$s}%")));

        $reports = $query->orderByDesc('report_date')->paginate(20)->withQueryString();

        $employees = Employee::orderBy('full_name')->get(['id', 'full_name']);
        $departments = Department::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $managers = Employee::whereHas('subordinates')->orderBy('full_name')->get(['id', 'full_name']);

        return view('admin.work-reports.index', compact('reports', 'employees', 'departments', 'projects', 'managers'));
    }
}
