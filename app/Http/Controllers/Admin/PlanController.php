<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\Project;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $query = Plan::with(['owner', 'department', 'employees'])
            ->when($request->filled('search'), fn($q) => $q->where('title', 'like', '%'.$request->search.'%'))
            ->when($request->filled('status'),     fn($q) => $q->where('status', $request->status))
            ->when($request->filled('priority'),   fn($q) => $q->where('priority', $request->priority))
            ->when($request->filled('category'),   fn($q) => $q->where('category', $request->category))
            ->when($request->filled('department_id'), fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->filled('employee_id'), fn($q) => $q->whereHas('employees', fn($eq) => $eq->where('employees.id', $request->employee_id)))
            ->when($request->filled('due_from'), fn($q) => $q->whereDate('due_date', '>=', $request->due_from))
            ->when($request->filled('due_to'),   fn($q) => $q->whereDate('due_date', '<=', $request->due_to));

        $plans = $query->latest()->paginate(20)->withQueryString();

        $summary = [
            'total'     => Plan::count(),
            'active'    => Plan::where('status', 'in_progress')->count(),
            'completed' => Plan::where('status', 'completed')->count(),
            'overdue'   => Plan::whereNotIn('status', ['completed','cancelled','archived'])->whereDate('due_date', '<', today())->count(),
            'due_today' => Plan::whereNotIn('status', ['completed','cancelled','archived'])->whereDate('due_date', today())->count(),
        ];

        $departments = Department::orderBy('name')->get();
        $employees   = Employee::where('employment_status', 'active')->orderBy('full_name')->get();

        return view('admin.plans.index', compact('plans', 'summary', 'departments', 'employees'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $projects    = Project::orderBy('name')->get();
        $employees   = Employee::where('employment_status', 'active')->orderBy('full_name')->get();

        return view('admin.plans.create', compact('departments', 'projects', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePlan($request);

        $plan = Plan::create(array_merge($validated, [
            'plan_number' => Plan::nextNumber(),
            'owner_id'    => auth()->id(),
        ]));

        if ($request->filled('employee_ids')) {
            $plan->employees()->sync($request->employee_ids);
            $this->notifyAssigned($plan, $request->employee_ids);
        }

        AuditLog::record('plan.created', $plan->id, ['title' => $plan->title]);

        return redirect()->route('admin.plans.show', $plan)
            ->with('message', 'Plan created successfully.');
    }

    public function show(Plan $plan)
    {
        $plan->load([
            'owner', 'department', 'project',
            'employees.department',
            'tasks.assignedTo',
            'comments.user',
            'attachments.uploadedBy',
        ]);

        $taskStats = [
            'total'       => $plan->tasks->count(),
            'completed'   => $plan->tasks->where('status', 'completed')->count(),
            'in_progress' => $plan->tasks->where('status', 'in_progress')->count(),
            'pending'     => $plan->tasks->where('status', 'pending')->count(),
            'overdue'     => $plan->tasks->filter(fn($t) => $t->is_overdue)->count(),
        ];

        $employees = Employee::where('employment_status', 'active')->orderBy('full_name')->get();

        return view('admin.plans.show', compact('plan', 'taskStats', 'employees'));
    }

    public function edit(Plan $plan)
    {
        $departments = Department::orderBy('name')->get();
        $projects    = Project::orderBy('name')->get();
        $employees   = Employee::where('employment_status', 'active')->orderBy('full_name')->get();
        $plan->load('employees');

        return view('admin.plans.edit', compact('plan', 'departments', 'projects', 'employees'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $this->validatePlan($request, $plan);

        $wasStatus = $plan->status;
        $plan->update($validated);

        if ($request->filled('employee_ids')) {
            $plan->employees()->sync($request->employee_ids);
        } elseif ($request->has('employee_ids')) {
            $plan->employees()->detach();
        }

        if ($validated['status'] === 'completed' && $wasStatus !== 'completed') {
            $plan->update(['completion_date' => today()]);
        }

        AuditLog::record('plan.updated', $plan->id, ['title' => $plan->title, 'status' => $plan->status]);

        return redirect()->route('admin.plans.show', $plan)
            ->with('message', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        AuditLog::record('plan.deleted', $plan->id, ['title' => $plan->title]);
        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('message', 'Plan deleted.');
    }

    public function archive(Plan $plan)
    {
        $plan->update(['status' => 'archived']);
        AuditLog::record('plan.archived', $plan->id, []);

        return back()->with('message', 'Plan archived.');
    }

    public function duplicate(Plan $plan)
    {
        $new = $plan->replicate(['plan_number', 'status', 'progress', 'completion_date', 'actual_hours']);
        $new->plan_number = Plan::nextNumber();
        $new->title       = 'Copy of ' . $plan->title;
        $new->status      = 'draft';
        $new->progress    = 0;
        $new->owner_id    = auth()->id();
        $new->save();

        foreach ($plan->tasks as $task) {
            $t = $task->replicate(['status', 'completed_at']);
            $t->plan_id = $new->id;
            $t->status  = 'pending';
            $t->save();
        }

        $new->employees()->sync($plan->employees->pluck('id'));

        AuditLog::record('plan.duplicated', $new->id, ['source_id' => $plan->id]);

        return redirect()->route('admin.plans.show', $new)
            ->with('message', 'Plan duplicated. Review and publish when ready.');
    }

    private function validatePlan(Request $request, ?Plan $plan = null): array
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'category'        => 'required|in:daily,weekly,monthly,quarterly,annual,personal,team,project,strategic',
            'department_id'   => 'nullable|exists:departments,id',
            'project_id'      => 'nullable|exists:projects,id',
            'priority'        => 'required|in:low,medium,high,critical',
            'status'          => 'required|in:draft,pending,in_progress,on_hold,completed,cancelled,archived',
            'start_date'      => 'nullable|date',
            'due_date'        => 'nullable|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'tags'            => 'nullable|string',
            'notes'           => 'nullable|string',
        ]);

        // Convert comma-string to array for JSON cast
        if (isset($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_values(array_filter(array_map('trim', explode(',', $data['tags']))));
        }

        return $data;
    }

    private function notifyAssigned(Plan $plan, array $employeeIds): void
    {
        $employees = Employee::whereIn('id', $employeeIds)->whereNotNull('user_id')->get();
        foreach ($employees as $emp) {
            Notification::send(
                $emp->user_id,
                'plan_assigned',
                'New Plan Assigned',
                "You have been assigned to plan: {$plan->title}",
                $plan->id
            );
        }
    }
}
