<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    private function employeeForUser(Request $request): ?Employee
    {
        return Employee::where('user_id', $request->user()->id)
            ->where('employment_status', 'active')
            ->first();
    }

    public function index(Request $request)
    {
        $employee = $this->employeeForUser($request);

        if (!$employee) {
            return view('attendance.index', ['employee' => null]);
        }

        $month  = $request->integer('month', today()->month);
        $year   = $request->integer('year', today()->year);
        $start  = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end    = $start->copy()->endOfMonth();

        $records = $employee->attendances()
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get()
            ->keyBy(fn($a) => $a->date->toDateString());

        $stats = [
            'present'  => $records->where('status', 'present')->count(),
            'absent'   => $records->where('status', 'absent')->count(),
            'late'     => $records->where('status', 'late')->count(),
            'on_leave' => $records->where('status', 'on_leave')->count(),
        ];

        $today = $employee->attendances()->whereDate('date', today())->first();

        return view('attendance.index', compact('employee', 'records', 'stats', 'today', 'month', 'year', 'start', 'end'));
    }

    public function checkIn(Request $request)
    {
        $employee = $this->employeeForUser($request);

        if (!$employee) {
            return back()->with('message', 'No active employee profile found.');
        }

        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee->id,
            'date'        => today()->toDateString(),
        ]);

        if ($attendance->check_in_time) {
            return back()->with('message', 'You have already checked in today.');
        }

        $checkIn      = now()->format('H:i');
        $expectedIn   = '09:00'; // configurable in future
        $lateMinutes  = null;

        if ($checkIn > $expectedIn) {
            $lateMinutes = Carbon::createFromTimeString($expectedIn)
                ->diffInMinutes(Carbon::createFromTimeString($checkIn));
        }

        $attendance->fill([
            'status'        => $lateMinutes > 0 ? 'late' : 'present',
            'check_in_time' => $checkIn,
            'late_minutes'  => $lateMinutes,
            'marked_by'     => auth()->id(),
        ])->save();

        return back()->with('message', 'Checked in at ' . $checkIn . '.');
    }

    public function checkOut(Request $request)
    {
        $employee = $this->employeeForUser($request);

        if (!$employee) {
            return back()->with('message', 'No active employee profile found.');
        }

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', today())
            ->first();

        if (!$attendance || !$attendance->check_in_time) {
            return back()->with('message', 'You have not checked in today.');
        }

        if ($attendance->check_out_time) {
            return back()->with('message', 'You have already checked out today.');
        }

        $attendance->update(['check_out_time' => now()->format('H:i')]);

        return back()->with('message', 'Checked out at ' . now()->format('H:i') . '.');
    }
}
