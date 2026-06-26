<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $user     = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return view('plans.index', ['plans' => collect(), 'summary' => []]);
        }

        $query = $employee->plans()
            ->with(['owner', 'department', 'tasks'])
            ->when($request->filled('status'),   fn($q) => $q->where('status', $request->status))
            ->when($request->filled('priority'), fn($q) => $q->where('priority', $request->priority));

        $plans = $query->latest()->paginate(15)->withQueryString();

        $all = $employee->plans();
        $summary = [
            'total'     => $all->count(),
            'active'    => (clone $all)->where('status', 'in_progress')->count(),
            'completed' => (clone $all)->where('status', 'completed')->count(),
            'overdue'   => (clone $all)->whereNotIn('status', ['completed','cancelled','archived'])
                               ->whereDate('due_date', '<', today())->count(),
        ];

        return view('plans.index', compact('plans', 'summary'));
    }

    public function show(Plan $plan)
    {
        $user     = auth()->user();
        $employee = $user->employee;

        // Only assigned employees (or admins/editors) can view
        if ($employee) {
            abort_unless(
                $plan->employees()->where('employees.id', $employee->id)->exists(),
                403
            );
        } else {
            abort_unless($user->isAdmin() || $user->isEditor(), 403);
        }

        $plan->load([
            'owner', 'department', 'project',
            'employees',
            'tasks.assignedTo',
            'comments.user',
            'attachments',
        ]);

        return view('plans.show', compact('plan'));
    }
}
