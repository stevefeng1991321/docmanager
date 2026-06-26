<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions   = Position::with('department')->withCount('employees')->orderBy('title')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.positions.index', compact('positions', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
        ]);

        $exists = Position::where('title', $validated['title'])
            ->where('department_id', $validated['department_id'] ?? null)
            ->exists();

        if ($exists) {
            return back()->with('message', 'That position already exists in the selected department.')->with('status', 'error');
        }

        $position = Position::create($validated);

        AuditLog::record('position.created', $position->id, ['title' => $position->title]);

        return back()->with('message', "Position \"{$position->title}\" created.");
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
        ]);

        $exists = Position::where('title', $validated['title'])
            ->where('department_id', $validated['department_id'] ?? null)
            ->where('id', '!=', $position->id)
            ->exists();

        if ($exists) {
            return back()->with('message', 'That position already exists in the selected department.')->with('status', 'error');
        }

        $position->update($validated);

        AuditLog::record('position.updated', $position->id, ['title' => $position->title]);

        return back()->with('message', "Position \"{$position->title}\" updated.");
    }

    public function destroy(Position $position)
    {
        $count = $position->employees()->count();
        if ($count > 0) {
            return back()
                ->with('message', "Cannot delete \"{$position->title}\": {$count} employee(s) are assigned to it. Reassign them first.")
                ->with('status', 'error');
        }

        $title = $position->title;
        $position->delete();
        AuditLog::record('position.deleted', null, ['title' => $title]);

        return back()->with('message', "Position \"{$title}\" deleted.");
    }
}
