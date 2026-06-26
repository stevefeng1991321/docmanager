<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Plan;
use App\Models\PlanTask;
use Illuminate\Http\Request;

class PlanReportController extends Controller
{
    public function dashboard()
    {
        $summary = [
            'total'     => Plan::count(),
            'active'    => Plan::where('status', 'in_progress')->count(),
            'completed' => Plan::where('status', 'completed')->count(),
            'overdue'   => Plan::whereNotIn('status', ['completed','cancelled','archived'])
                               ->whereDate('due_date', '<', today())->count(),
            'due_today' => Plan::whereNotIn('status', ['completed','cancelled','archived'])
                               ->whereDate('due_date', today())->count(),
            'draft'     => Plan::where('status', 'draft')->count(),
            'on_hold'   => Plan::where('status', 'on_hold')->count(),
        ];

        $byStatus = Plan::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')->pluck('count', 'status');

        $byPriority = Plan::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')->pluck('count', 'priority');

        $byDepartment = Plan::selectRaw('department_id, COUNT(*) as count')
            ->with('department')
            ->whereNotNull('department_id')
            ->groupBy('department_id')
            ->get();

        $recentPlans = Plan::with(['owner', 'department'])
            ->latest()->limit(8)->get();

        $overduePlans = Plan::with(['owner', 'department'])
            ->whereNotIn('status', ['completed','cancelled','archived'])
            ->whereDate('due_date', '<', today())
            ->orderBy('due_date')
            ->limit(8)->get();

        $taskStats = [
            'total'     => PlanTask::count(),
            'completed' => PlanTask::where('status', 'completed')->count(),
            'overdue'   => PlanTask::where('status', '!=', 'completed')
                               ->whereDate('due_date', '<', today())->count(),
        ];

        return view('admin.plans.dashboard', compact(
            'summary', 'byStatus', 'byPriority', 'byDepartment',
            'recentPlans', 'overduePlans', 'taskStats'
        ));
    }
}
