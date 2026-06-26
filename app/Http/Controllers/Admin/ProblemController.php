<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
            'function_name' => $problem->function_name,
            'test_cases'    => $problem->test_cases,
        ]);
    }

    public function create()
    {
        return view('admin.problems.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateProblem($request);

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
        $validated = $this->validateProblem($request);

        $problem->update($validated);

        return redirect()->route('admin.problems.index')
            ->with('message', 'Problem updated.');
    }

    private function validateProblem(Request $request): array
    {
        $validated = $request->validate([
            'order_index'       => ['required', 'integer', 'min:1'],
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['required', 'string'],
            'difficulty'        => ['required', 'in:easy,medium,hard'],
            'category'          => ['required', 'string', 'max:100'],
            'solution_code'     => ['nullable', 'string'],
            'function_name'     => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z_$][A-Za-z0-9_$]*$/'],
            'test_cases'        => ['nullable', 'array'],
            'test_cases.*.args'     => ['required', 'string'],
            'test_cases.*.expected' => ['required', 'string'],
        ]);

        $testCases = [];
        foreach ($validated['test_cases'] ?? [] as $i => $case) {
            $args = json_decode($case['args'], true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($args)) {
                throw ValidationException::withMessages([
                    "test_cases.{$i}.args" => 'Args must be a valid JSON array, e.g. [4] or ["hello"].',
                ]);
            }

            $expected = json_decode($case['expected'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages([
                    "test_cases.{$i}.expected" => 'Expected must be valid JSON, e.g. "Even", 4, true, or [0,1].',
                ]);
            }

            $testCases[] = ['args' => $args, 'expected' => $expected];
        }

        $validated['test_cases'] = $testCases;

        return $validated;
    }

    public function destroy(Problem $problem)
    {
        $problem->delete();
        return redirect()->route('admin.problems.index')
            ->with('message', 'Problem deleted.');
    }
}
