<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Project;
use App\Models\WorkReport;
use App\Models\WorkReportAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkReportController extends Controller
{
    public function index(Request $request)
    {
        $employee = $request->user()->employee;
        $isManager = $employee && $employee->subordinates()->exists();
        $tab = $isManager ? $request->input('tab', 'mine') : 'mine';

        $query = WorkReport::with(['employee', 'project']);

        if ($tab === 'team' && $isManager) {
            $query->whereIn('employee_id', $employee->subordinates()->pluck('id'))
                ->where('status', '!=', 'draft');
        } else {
            $query->where('employee_id', $employee?->id ?? 0);
        }

        $query->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->project_id, fn ($q, $p) => $q->where('project_id', $p))
            ->when($request->date_from, fn ($q, $d) => $q->whereDate('report_date', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->whereDate('report_date', '<=', $d))
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('title', 'like', "%{$s}%")
                ->orWhere('tasks_completed', 'like', "%{$s}%")
                ->orWhere('notes', 'like', "%{$s}%")));

        $reports = $query->orderByDesc('report_date')->paginate(20)->withQueryString();
        $projects = Project::orderBy('name')->get();

        return view('work-reports.index', compact('reports', 'projects', 'isManager', 'tab', 'employee'));
    }

    public function create(Request $request)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 403, 'You need an employee profile before you can create work reports.');

        $projects = Project::orderBy('name')->get();

        return view('work-reports.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $employee = $request->user()->employee;
        abort_unless($employee, 403, 'You need an employee profile before you can create work reports.');

        $validated = $this->validateData($request);

        $report = WorkReport::create([
            ...$validated,
            'employee_id' => $employee->id,
            'status' => 'draft',
        ]);

        $this->syncTasks($report, $validated['tasks'] ?? []);

        AuditLog::record('work_report.created', $report->id, ['title' => $report->title]);

        if ($request->input('action') === 'submit') {
            $this->transitionToSubmitted($report);
        }

        return redirect()->route('work-reports.show', $report)->with('message', 'Work report ' . ($request->input('action') === 'submit' ? 'submitted.' : 'saved as draft.'));
    }

    public function show(Request $request, WorkReport $workReport)
    {
        abort_unless($workReport->canBeViewedBy($request->user()), 403);

        $workReport->load(['employee.user', 'employee.manager', 'project', 'tasks', 'comments.user', 'attachments.uploadedBy', 'reviewedBy']);

        return view('work-reports.show', compact('workReport'));
    }

    public function edit(Request $request, WorkReport $workReport)
    {
        abort_unless($workReport->canBeEditedBy($request->user()), 403);

        $workReport->load('tasks');
        $projects = Project::orderBy('name')->get();

        return view('work-reports.edit', compact('workReport', 'projects'));
    }

    public function update(Request $request, WorkReport $workReport)
    {
        abort_unless($workReport->canBeEditedBy($request->user()), 403);

        $validated = $this->validateData($request);
        $wasRejected = $workReport->isRejected();

        $workReport->update($validated);
        $this->syncTasks($workReport, $validated['tasks'] ?? []);

        AuditLog::record('work_report.updated', $workReport->id, ['title' => $workReport->title]);

        if ($request->input('action') === 'submit') {
            $this->transitionToSubmitted($workReport, $wasRejected);
        }

        return redirect()->route('work-reports.show', $workReport)->with('message', 'Work report updated.');
    }

    public function destroy(Request $request, WorkReport $workReport)
    {
        abort_unless($workReport->canBeEditedBy($request->user()), 403);

        $title = $workReport->title;
        $workReport->delete();

        AuditLog::record('work_report.deleted', null, ['title' => $title]);

        return redirect()->route('work-reports.index')->with('message', "Report \"{$title}\" deleted.");
    }

    public function duplicate(Request $request, WorkReport $workReport)
    {
        abort_unless($workReport->isOwnedBy($request->user()), 403);

        $copy = WorkReport::create([
            'employee_id' => $workReport->employee_id,
            'title' => $workReport->title . ' (Copy)',
            'type' => $workReport->type,
            'report_date' => now()->toDateString(),
            'project_id' => $workReport->project_id,
            'client_name' => $workReport->client_name,
            'tasks_completed' => $workReport->tasks_completed,
            'task_descriptions' => $workReport->task_descriptions,
            'challenges' => $workReport->challenges,
            'solutions' => $workReport->solutions,
            'notes' => $workReport->notes,
            'work_hours' => $workReport->work_hours,
            'overall_progress' => $workReport->overall_progress,
            'status' => 'draft',
        ]);

        foreach ($workReport->tasks as $task) {
            $copy->tasks()->create([
                'title' => $task->title,
                'status' => 'planned',
                'priority' => $task->priority,
                'completion_percent' => 0,
                'time_spent_hours' => null,
                'order_index' => $task->order_index,
            ]);
        }

        AuditLog::record('work_report.duplicated', $copy->id, ['from' => $workReport->id]);

        return redirect()->route('work-reports.edit', $copy)->with('message', 'Report duplicated as a new draft.');
    }

    public function submit(Request $request, WorkReport $workReport)
    {
        abort_unless($workReport->canBeEditedBy($request->user()), 403);

        $wasRejected = $workReport->isRejected();
        $this->transitionToSubmitted($workReport, $wasRejected);

        return redirect()->route('work-reports.show', $workReport)->with('message', 'Work report submitted.');
    }

    public function review(Request $request, WorkReport $workReport)
    {
        $user = $request->user();
        abort_unless($workReport->canBeReviewedBy($user) && in_array($workReport->status, ['submitted', 'under_review']), 403);

        $validated = $request->validate([
            'decision' => ['required', 'in:under_review,approved,rejected'],
            'comment'  => ['nullable', 'string'],
        ]);

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
                "Your report \"{$workReport->title}\" was {$validated['decision']}" . (!empty($validated['comment']) ? ": {$validated['comment']}" : '.'),
                $workReport->id
            );
        }

        return redirect()->route('work-reports.show', $workReport)->with('message', 'Review saved.');
    }

    public function storeComment(Request $request, WorkReport $workReport)
    {
        $user = $request->user();
        abort_unless($workReport->canBeViewedBy($user), 403);

        $isReviewer = $workReport->canBeReviewedBy($user);

        $validated = $request->validate([
            'body' => ['required', 'string'],
            'type' => ['nullable', 'in:comment,feedback,revision_request'],
        ]);

        $type = $isReviewer ? ($validated['type'] ?? 'comment') : 'comment';

        $workReport->comments()->create([
            'user_id' => $user->id,
            'type'    => $type,
            'body'    => $validated['body'],
        ]);

        AuditLog::record('work_report.comment_added', $workReport->id, ['type' => $type]);

        $notifyUserId = $isReviewer ? $workReport->employee->user_id : $workReport->employee->manager?->user_id;

        if ($notifyUserId) {
            Notification::send(
                $notifyUserId,
                $type === 'revision_request' ? 'report_revision_requested' : 'report_feedback_added',
                $type === 'revision_request' ? 'Revision requested' : 'New comment on your report',
                "{$user->name} commented on \"{$workReport->title}\": {$validated['body']}",
                $workReport->id
            );
        }

        return redirect()->route('work-reports.show', $workReport)->with('message', 'Comment added.');
    }

    public function storeAttachment(Request $request, WorkReport $workReport)
    {
        $user = $request->user();
        abort_unless($workReport->canBeViewedBy($user), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file'  => ['required', 'file', 'max:10240'],
        ]);

        $file = $request->file('file');
        $storedName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs("work-reports/{$workReport->id}/attachments", $storedName, 'local');

        $attachment = $workReport->attachments()->create([
            'title'       => $validated['title'],
            'file_path'   => $path,
            'uploaded_by' => $user->id,
        ]);

        AuditLog::record('work_report.attachment_uploaded', $workReport->id, ['title' => $attachment->title]);

        return back()->with('message', "Attachment \"{$attachment->title}\" uploaded.");
    }

    public function downloadAttachment(Request $request, WorkReport $workReport, WorkReportAttachment $attachment)
    {
        abort_unless($workReport->canBeViewedBy($request->user()), 403);
        abort_unless($attachment->work_report_id === $workReport->id, 404);
        abort_if(!Storage::disk('local')->exists($attachment->file_path), 404);

        return Storage::disk('local')->download($attachment->file_path, $attachment->title);
    }

    public function previewAttachment(Request $request, WorkReport $workReport, WorkReportAttachment $attachment)
    {
        abort_unless($workReport->canBeViewedBy($request->user()), 403);
        abort_unless($attachment->work_report_id === $workReport->id, 404);
        abort_unless($attachment->isImage(), 404);
        abort_if(!Storage::disk('local')->exists($attachment->file_path), 404);

        return Storage::disk('local')->response($attachment->file_path);
    }

    public function destroyAttachment(Request $request, WorkReport $workReport, WorkReportAttachment $attachment)
    {
        abort_unless($workReport->canBeViewedBy($request->user()), 403);
        abort_unless($attachment->work_report_id === $workReport->id, 404);

        Storage::disk('local')->delete($attachment->file_path);
        $title = $attachment->title;
        $attachment->delete();

        AuditLog::record('work_report.attachment_deleted', $workReport->id, ['title' => $title]);

        return back()->with('message', "Attachment \"{$title}\" deleted.");
    }

    private function transitionToSubmitted(WorkReport $report, bool $wasRejected = false): void
    {
        $report->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        AuditLog::record($wasRejected ? 'work_report.resubmitted' : 'work_report.submitted', $report->id, ['title' => $report->title]);

        $manager = $report->employee->manager;
        if ($manager && $manager->user_id) {
            Notification::send(
                $manager->user_id,
                'report_submitted',
                'New work report submitted',
                "{$report->employee->full_name} submitted \"{$report->title}\" for review.",
                $report->id
            );
        }
    }

    private function syncTasks(WorkReport $report, array $tasks): void
    {
        $report->tasks()->delete();

        foreach (array_values($tasks) as $i => $task) {
            if (empty($task['title'])) {
                continue;
            }

            $report->tasks()->create([
                'title' => $task['title'],
                'status' => $task['status'] ?? 'planned',
                'priority' => $task['priority'] ?? 'medium',
                'completion_percent' => (int) ($task['completion_percent'] ?? 0),
                'time_spent_hours' => $task['time_spent_hours'] ?? null,
                'order_index' => $i + 1,
            ]);
        }
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:daily,weekly,monthly'],
            'report_date' => ['required', 'date'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'tasks_completed' => ['nullable', 'string'],
            'task_descriptions' => ['nullable', 'string'],
            'challenges' => ['nullable', 'string'],
            'solutions' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'work_hours' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'overall_progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'tasks' => ['nullable', 'array'],
            'tasks.*.title' => ['nullable', 'string', 'max:255'],
            'tasks.*.status' => ['nullable', 'in:completed,in_progress,planned'],
            'tasks.*.priority' => ['nullable', 'in:low,medium,high'],
            'tasks.*.completion_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'tasks.*.time_spent_hours' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
        ]);
    }
}
