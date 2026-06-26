<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('department')->withCount('workReports')->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.projects.index', compact('projects', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:projects,name'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'description'   => ['nullable', 'string'],
            'status'        => ['required', 'in:active,completed,on_hold'],
        ]);

        $project = Project::create($validated);

        AuditLog::record('project.created', $project->id, ['name' => $project->name]);

        return back()->with('message', "Project \"{$project->name}\" created.");
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:projects,name,' . $project->id],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'description'   => ['nullable', 'string'],
            'status'        => ['required', 'in:active,completed,on_hold'],
        ]);

        $project->update($validated);

        AuditLog::record('project.updated', $project->id, ['name' => $project->name]);

        return back()->with('message', "Project \"{$project->name}\" updated.");
    }

    public function destroy(Project $project)
    {
        $count = $project->workReports()->count();
        if ($count > 0) {
            return back()
                ->with('message', "Cannot delete \"{$project->name}\": {$count} work report(s) reference it.")
                ->with('status', 'error');
        }

        $name = $project->name;
        $project->delete();
        AuditLog::record('project.deleted', null, ['name' => $name]);

        return back()->with('message', "Project \"{$name}\" deleted.");
    }
}
