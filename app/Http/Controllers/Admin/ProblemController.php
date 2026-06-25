<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
    public function index()
    {
        $problems = Problem::orderBy('order_index')
            ->select(['id', 'order_index', 'title', 'difficulty', 'category'])
            ->get();

        return view('admin.problems.index', compact('problems'));
    }

    public function show(Problem $problem)
    {
        return response()->json([
            'id'            => $problem->id,
            'title'         => $problem->title,
            'description'   => $problem->description,
            'difficulty'    => $problem->difficulty,
            'category'      => $problem->category,
            'solution_code' => $problem->solution_code,
        ]);
    }

    public function create()
    {
        return view('admin.problems.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_index'   => ['required', 'integer', 'min:1'],
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string'],
            'difficulty'    => ['required', 'in:easy,medium,hard'],
            'category'      => ['required', 'string', 'max:100'],
            'solution_code' => ['nullable', 'string'],
        ]);

        Problem::create($validated);

        return redirect()->route('admin.problems.index')
            ->with('message', 'Problem created.');
    }

    public function edit(Problem $problem)
    {
        return view('admin.problems.edit', compact('problem'));
    }

    public function update(Request $request, Problem $problem)
    {
        $validated = $request->validate([
            'order_index'   => ['required', 'integer', 'min:1'],
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string'],
            'difficulty'    => ['required', 'in:easy,medium,hard'],
            'category'      => ['required', 'string', 'max:100'],
            'solution_code' => ['nullable', 'string'],
        ]);

        $problem->update($validated);

        return redirect()->route('admin.problems.index')
            ->with('message', 'Problem updated.');
    }

    public function destroy(Problem $problem)
    {
        $problem->delete();
        return redirect()->route('admin.problems.index')
            ->with('message', 'Problem deleted.');
    }
}
