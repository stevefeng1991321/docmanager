<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLeave;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceLeaveController extends Controller
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
            return view('attendance.leaves', ['employee' => null, 'leaves' => collect()]);
        }

        $leaves = $employee->leaves()
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('attendance.leaves', compact('employee', 'leaves'));
    }

    public function store(Request $request)
    {
        $employee = $this->employeeForUser($request);

        if (!$employee) {
            return back()->with(['status' => 'error', 'message' => 'No active employee profile found.']);
        }

        $validated = $request->validate([
            'leave_type' => 'required|in:annual,sick,personal,unpaid,maternity,paternity',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'nullable|string|max:1000',
        ]);

        // Calculate working days
        $start   = \Carbon\Carbon::parse($validated['start_date']);
        $end     = \Carbon\Carbon::parse($validated['end_date']);
        $days    = 0;
        $current = $start->copy();
        while ($current <= $end) {
            if (!$current->isWeekend()) $days++;
            $current->addDay();
        }

        $employee->leaves()->create([
            ...$validated,
            'days_count' => $days,
            'status'     => 'pending',
        ]);

        return back()->with('message', 'Leave request submitted successfully.');
    }

    public function destroy(Request $request, AttendanceLeave $leave)
    {
        $employee = $this->employeeForUser($request);

        if (!$employee || $leave->employee_id !== $employee->id) {
            abort(403);
        }

        if (!$leave->isPending()) {
            return back()->with(['status' => 'error', 'message' => 'Only pending requests can be cancelled.']);
        }

        $leave->update(['status' => 'cancelled']);

        return back()->with('message', 'Leave request cancelled.');
    }
}
