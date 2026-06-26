<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Notification;
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

        $employees   = Employee::orderBy('full_name')->get(['id', 'full_name']);
        $departments = Department::orderBy('name')->get();
        $projects    = Project::orderBy('name')->get();
        $managers    = Employee::whereHas('subordinates')->orderBy('full_name')->get(['id', 'full_name']);

        return view('admin.work-reports.index', compact('reports', 'employees', 'departments', 'projects', 'managers'));
    }

    public function show(WorkReport $workReport)
    {
        $workReport->load([
            'employee.user', 'employee.department', 'employee.manager',
            'project', 'tasks', 'comments.user', 'attachments.uploadedBy', 'reviewedBy',
        ]);

        return view('admin.work-reports.show', compact('workReport'));
    }

    public function review(Request $request, WorkReport $workReport)
    {
        abort_unless(in_array($workReport->status, ['submitted', 'under_review']), 422);

        $validated = $request->validate([
            'decision' => ['required', 'in:under_review,approved,rejected'],
            'comment'  => ['nullable', 'string'],
        ]);

        $user = $request->user();

        $workReport->update(array_filter([
            'status'      => $validated['decision'],
            'reviewed_at' => $validated['decision'] !== 'under_review' ? now() : null,
            'reviewed_by' => $validated['decision'] !== 'under_review' ? $user->id : null,
        ]));

        if (!empty($validated['comment'])) {
            $workReport->comments()->create([
                'user_id' => $user->id,
                'type'    => 'comment',
                'body'    => $validated['comment'],
            ]);
        }

        AuditLog::record('work_report.reviewed', $workReport->id, ['decision' => $validated['decision']]);

        if (in_array($validated['decision'], ['approved', 'rejected']) && $workReport->employee->user_id) {
            Notification::send(
                $workReport->employee->user_id,
                $validated['decision'] === 'approved' ? 'report_approved' : 'report_rejected',
                'Work report ' . $validated['decision'],
                "Your report \"{$workReport->title}\" was {$validated['decision']}" .
                    (!empty($validated['comment']) ? ": {$validated['comment']}" : '.'),
                $workReport->id
            );
        }

        return redirect()->route('admin.work-reports.show', $workReport)
            ->with('message', 'Review decision saved.');
    }

    public function storeComment(Request $request, WorkReport $workReport)
    {
        $validated = $request->validate([
            'body' => ['required', 'string'],
            'type' => ['nullable', 'in:comment,feedback,revision_request'],
        ]);

        $user = $request->user();
        $type = $validated['type'] ?? 'comment';

        $workReport->comments()->create([
            'user_id' => $user->id,
            'type'    => $type,
            'body'    => $validated['body'],
        ]);

        AuditLog::record('work_report.comment_added', $workReport->id, ['type' => $type]);

        if ($workReport->employee->user_id) {
            Notification::send(
                $workReport->employee->user_id,
                $type === 'revision_request' ? 'report_revision_requested' : 'report_feedback_added',
                $type === 'revision_request' ? 'Revision requested' : 'New comment on your report',
                "{$user->name} commented on \"{$workReport->title}\": {$validated['body']}",
                $workReport->id
            );
        }

        return redirect()->route('admin.work-reports.show', $workReport)
            ->with('message', 'Comment posted.');
    }
}
