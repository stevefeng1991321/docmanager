<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\AttendanceLeave;
use App\Models\Attendance;
use App\Models\Notification;
use Illuminate\Http\Request;

class AttendanceLeaveController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $leaves = AttendanceLeave::with(['employee.department', 'approvedBy'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->when($request->filled('employee_id'), fn($q) => $q->where('employee_id', $request->employee_id))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $counts = AttendanceLeave::selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('admin.attendance.leaves', compact('leaves', 'status', 'counts'));
    }

    public function approve(AttendanceLeave $leave)
    {
        if (!$leave->isPending()) {
            return back()->with('message', 'Leave request is not pending.');
        }

        $leave->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Auto-create attendance records for the leave period
        $current = $leave->start_date->copy();
        while ($current <= $leave->end_date) {
            if (!$current->isWeekend()) {
                Attendance::updateOrCreate(
                    ['employee_id' => $leave->employee_id, 'date' => $current->toDateString()],
                    ['status' => 'on_leave', 'marked_by' => auth()->id()]
                );
            }
            $current->addDay();
        }

        if ($leave->employee->user_id) {
            Notification::send(
                $leave->employee->user_id,
                'leave_approved',
                'Leave Request Approved',
                "Your {$leave->leave_type_label} from {$leave->start_date->format('M d')} to {$leave->end_date->format('M d, Y')} has been approved.",
                $leave->id
            );
        }

        AuditLog::record('attendance.leave_approved', $leave->id, ['employee_id' => $leave->employee_id]);

        return back()->with('message', 'Leave request approved.');
    }

    public function reject(Request $request, AttendanceLeave $leave)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        if (!$leave->isPending()) {
            return back()->with('message', 'Leave request is not pending.');
        }

        $leave->update([
            'status'           => 'rejected',
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        if ($leave->employee->user_id) {
            Notification::send(
                $leave->employee->user_id,
                'leave_rejected',
                'Leave Request Rejected',
                "Your {$leave->leave_type_label} request has been rejected. Reason: {$request->rejection_reason}",
                $leave->id
            );
        }

        AuditLog::record('attendance.leave_rejected', $leave->id, ['employee_id' => $leave->employee_id]);

        return back()->with('message', 'Leave request rejected.');
    }

    public function destroy(AttendanceLeave $leave)
    {
        AuditLog::record('attendance.leave_deleted', $leave->id, ['employee_id' => $leave->employee_id]);
        $leave->delete();

        return back()->with('message', 'Leave record deleted.');
    }
}
