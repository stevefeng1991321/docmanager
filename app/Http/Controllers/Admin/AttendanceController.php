<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)->toDateString()
            : today()->toDateString();

        $employees = Employee::where('employment_status', 'active')
            ->with([
                'department',
                'attendances' => fn($q) => $q->whereDate('date', $date),
            ])
            ->orderBy('full_name')
            ->get()
            ->map(function ($emp) {
                $emp->today_attendance = $emp->attendances->first();
                return $emp;
            });

        $summary = [
            'total'      => $employees->count(),
            'present'    => $employees->filter(fn($e) => $e->today_attendance?->status === 'present')->count(),
            'absent'     => $employees->filter(fn($e) => $e->today_attendance?->status === 'absent')->count(),
            'late'       => $employees->filter(fn($e) => $e->today_attendance?->status === 'late')->count(),
            'on_leave'   => $employees->filter(fn($e) => $e->today_attendance?->status === 'on_leave')->count(),
            'not_marked' => $employees->filter(fn($e) => !$e->today_attendance)->count(),
        ];

        return view('admin.attendance.index', compact('employees', 'date', 'summary'));
    }

    public function mark(Request $request)
    {
        $validated = $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'date'           => 'required|date',
            'status'         => 'required|in:present,absent,late,on_leave,holiday,half_day',
            'check_in_time'  => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'late_minutes'   => 'nullable|integer|min:0|max:480',
            'notes'          => 'nullable|string|max:500',
        ]);

        $attendance = Attendance::updateOrCreate(
            ['employee_id' => $validated['employee_id'], 'date' => $validated['date']],
            array_merge($validated, ['marked_by' => auth()->id()])
        );

        AuditLog::record('attendance.marked', $attendance->id, [
            'employee_id' => $validated['employee_id'],
            'date'        => $validated['date'],
            'status'      => $validated['status'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'attendance' => $attendance]);
        }

        return back()->with('message', 'Attendance marked successfully.');
    }

    public function bulkMark(Request $request)
    {
        $validated = $request->validate([
            'date'   => 'required|date',
            'status' => 'required|in:present,absent,late,on_leave,holiday,half_day',
        ]);

        $employees = Employee::where('employment_status', 'active')
            ->whereDoesntHave('attendances', fn($q) => $q->whereDate('date', $validated['date']))
            ->pluck('id');

        foreach ($employees as $empId) {
            Attendance::create([
                'employee_id' => $empId,
                'date'        => $validated['date'],
                'status'      => $validated['status'],
                'marked_by'   => auth()->id(),
            ]);
        }

        AuditLog::record('attendance.bulk_marked', null, [
            'date'   => $validated['date'],
            'status' => $validated['status'],
            'count'  => $employees->count(),
        ]);

        return back()->with('message', "Bulk marked {$employees->count()} employees as {$validated['status']}.");
    }

    public function export(Request $request)
    {
        $month      = $request->integer('month', today()->month);
        $year       = $request->integer('year', today()->year);
        $employeeId = $request->input('employee_id');
        $deptId     = $request->input('department_id');
        $date       = $request->input('date'); // single-day export from daily view

        if ($date) {
            $start = Carbon::parse($date)->startOfDay();
            $end   = Carbon::parse($date)->endOfDay();
            $filename = 'attendance_' . $date . '.csv';
        } else {
            $start    = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end      = $start->copy()->endOfMonth();
            $filename = 'attendance_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.csv';
        }

        $records = Attendance::with('employee.department')
            ->whereBetween('date', [$start, $end])
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->when($deptId, fn($q) => $q->whereHas('employee', fn($eq) => $eq->where('department_id', $deptId)))
            ->orderBy('date')
            ->orderBy('employee_id')
            ->get();

        AuditLog::record('attendance.exported', null, [
            'start' => $start->toDateString(),
            'end'   => $end->toDateString(),
            'count' => $records->count(),
        ]);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-store',
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Date', 'Employee Code', 'Employee Name', 'Department',
                'Status', 'Check In', 'Check Out', 'Work Duration', 'Late (min)', 'Notes',
            ]);

            foreach ($records as $rec) {
                fputcsv($handle, [
                    $rec->date->format('Y-m-d'),
                    $rec->employee->employee_code,
                    $rec->employee->full_name,
                    $rec->employee->department?->name ?? '',
                    $rec->status_label,
                    $rec->check_in_time  ?? '',
                    $rec->check_out_time ?? '',
                    $rec->work_duration  ?? '',
                    $rec->late_minutes   ?? '',
                    $rec->notes          ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function report(Request $request)
    {
        $month      = $request->integer('month', today()->month);
        $year       = $request->integer('year', today()->year);
        $employeeId = $request->input('employee_id');
        $deptId     = $request->input('department_id');

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();
        $workingDays = $this->countWorkingDays($start, $end);

        $query = Attendance::with('employee.department')
            ->whereBetween('date', [$start, $end])
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->when($deptId, fn($q) => $q->whereHas('employee', fn($eq) => $eq->where('department_id', $deptId)))
            ->orderBy('date', 'desc');

        $records = $query->paginate(50)->withQueryString();

        $stats = Attendance::whereBetween('date', [$start, $end])
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->when($deptId, fn($q) => $q->whereHas('employee', fn($eq) => $eq->where('department_id', $deptId)))
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        $departments = \App\Models\Department::orderBy('name')->get();
        $employees   = Employee::where('employment_status', 'active')->orderBy('full_name')->get();

        return view('admin.attendance.report', compact(
            'records', 'stats', 'workingDays',
            'month', 'year', 'start', 'end',
            'departments', 'employees',
            'employeeId', 'deptId'
        ));
    }

    private function countWorkingDays(Carbon $start, Carbon $end): int
    {
        $days = 0;
        $current = $start->copy();
        while ($current <= $end) {
            if (!$current->isWeekend()) $days++;
            $current->addDay();
        }
        return $days;
    }
}
