<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('employees')->orderBy('name')->get();

        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:departments,name'],
            'description' => ['nullable', 'string'],
        ]);

        $department = Department::create($validated);

        AuditLog::record('department.created', $department->id, ['name' => $department->name]);

        return back()->with('message', "Department \"{$department->name}\" created.");
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:departments,name,' . $department->id],
            'description' => ['nullable', 'string'],
        ]);

        $department->update($validated);

        AuditLog::record('department.updated', $department->id, ['name' => $department->name]);

        return back()->with('message', "Department \"{$department->name}\" updated.");
    }

    public function destroy(Department $department)
    {
        $count = $department->employees()->count();
        if ($count > 0) {
            return back()
                ->with('message', "Cannot delete \"{$department->name}\": {$count} employee(s) are assigned to it. Reassign them first.")
                ->with('status', 'error');
        }

        $name = $department->name;
        $department->delete();
        AuditLog::record('department.deleted', null, ['name' => $name]);

        return back()->with('message', "Department \"{$name}\" deleted.");
    }
}
