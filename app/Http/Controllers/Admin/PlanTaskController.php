<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Plan;
use App\Models\PlanTask;
use Illuminate\Http\Request;

class PlanTaskController extends Controller
{
    public function store(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:employees,id',
            'priority'    => 'required|in:low,medium,high,critical',
            'start_date'  => 'nullable|date',
            'due_date'    => 'nullable|date',
            'notes'       => 'nullable|string',
        ]);

        $maxOrder = $plan->tasks()->max('sort_order') ?? 0;

        $plan->tasks()->create(array_merge($validated, [
            'status'     => 'pending',
            'sort_order' => $maxOrder + 1,
        ]));

        $plan->updateProgress();

        AuditLog::record('plan.task_added', $plan->id, ['title' => $validated['title']]);

        return back()->with('message', 'Task added.');
    }

    public function update(Request $request, Plan $plan, PlanTask $task)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:employees,id',
            'priority'    => 'required|in:low,medium,high,critical',
            'status'      => 'required|in:pending,in_progress,completed,cancelled',
            'start_date'  => 'nullable|date',
            'due_date'    => 'nullable|date',
            'notes'       => 'nullable|string',
        ]);

        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);
        $plan->updateProgress();

        return back()->with('message', 'Task updated.');
    }

    public function toggle(Plan $plan, PlanTask $task)
    {
        $newStatus = $task->status === 'completed' ? 'pending' : 'completed';

        $task->update([
            'status'       => $newStatus,
            'completed_at' => $newStatus === 'completed' ? now() : null,
        ]);

        $plan->updateProgress();

        return response()->json([
            'status'   => $newStatus,
            'progress' => $plan->fresh()->progress,
        ]);
    }

    public function destroy(Plan $plan, PlanTask $task)
    {
        $task->delete();
        $plan->updateProgress();

        return back()->with('message', 'Task deleted.');
    }
}
