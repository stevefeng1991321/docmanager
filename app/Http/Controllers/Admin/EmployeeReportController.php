<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeReportController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department', 'position'])->orderBy('full_name')->get();

        $statusCounts = Employee::query()
            ->selectRaw('employment_status, count(*) as total')
            ->groupBy('employment_status')
            ->pluck('total', 'employment_status');

        $byDepartment = Department::withCount('employees')->orderByDesc('employees_count')->get();

        $unassignedCount = Employee::whereNull('department_id')->count();

        $rosterByDepartment = $employees
            ->groupBy(fn (Employee $e) => $e->department?->name ?? 'Unassigned')
            ->sortKeys();

        $newHires = Employee::with(['department', 'position'])
            ->where('date_of_joining', '>=', now()->subDays(30))
            ->orderByDesc('date_of_joining')
            ->get();

        return view('admin.employee-reports.index', compact(
            'employees', 'statusCounts', 'byDepartment', 'unassignedCount', 'newHires', 'rosterByDepartment'
        ));
    }

    public function export(Request $request)
    {
        $employees = Employee::with(['department', 'position'])
            ->when($request->department, fn ($q, $d) => $q->where('department_id', $d))
            ->when($request->status, fn ($q, $s) => $q->where('employment_status', $s))
            ->orderBy('full_name')
            ->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="employee-directory.csv"'];

        $callback = function () use ($employees) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Employee Code', 'Full Name', 'Email', 'Department', 'Position', 'Status', 'Joining Date', 'Employment Type']);
            foreach ($employees as $employee) {
                fputcsv($out, [
                    $employee->employee_code,
                    $employee->full_name,
                    $employee->email ?? '—',
                    $employee->department?->name ?? '—',
                    $employee->position?->title ?? '—',
                    $employee->employment_status,
                    $employee->date_of_joining?->format('Y-m-d') ?? '—',
                    $employee->employment_type ?? '—',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
