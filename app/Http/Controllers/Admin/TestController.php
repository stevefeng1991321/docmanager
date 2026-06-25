<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TestController extends Controller
{
    public function index()
    {
        $tests = Test::withCount(['problems', 'invites'])
            ->latest()
            ->get();

        return view('admin.tests.index', compact('tests'));
    }

    public function create()
    {
        $problemBank = Problem::orderBy('category')->orderBy('order_index')->get();

        return view('admin.tests.create', compact('problemBank'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTest($request, 'draft,active');

        $test = Test::create([
            'title'               => $validated['title'],
            'description'         => $validated['description'] ?? null,
            'time_limit_minutes'  => $validated['time_limit_minutes'],
            'status'              => $validated['status'],
            'created_by'          => $request->user()->id,
        ]);

        $this->applyProblems($test, $validated['problems'] ?? [], $request->input('points', []), $validated['new_problems'] ?? []);

        return redirect()->route('admin.tests.show', $test)
            ->with('message', 'Test created.');
    }

    public function show(Test $test)
    {
        $test->load(['problems', 'invites' => fn ($q) => $q->latest()]);

        return view('admin.tests.show', compact('test'));
    }

    public function edit(Test $test)
    {
        $test->load('problems');
        $problemBank = Problem::orderBy('category')->orderBy('order_index')->get();
        $selectedIds = $test->problems->pluck('id')->all();
        $pointsById  = $test->problems->pluck('pivot.points', 'id')->all();

        return view('admin.tests.edit', compact('test', 'problemBank', 'selectedIds', 'pointsById'));
    }

    public function update(Request $request, Test $test)
    {
        $validated = $this->validateTest($request, 'draft,active,archived');

        $test->update([
            'title'               => $validated['title'],
            'description'         => $validated['description'] ?? null,
            'time_limit_minutes'  => $validated['time_limit_minutes'],
            'status'              => $validated['status'],
        ]);

        $this->applyProblems($test, $validated['problems'] ?? [], $request->input('points', []), $validated['new_problems'] ?? []);

        return redirect()->route('admin.tests.show', $test)
            ->with('message', 'Test updated.');
    }

    public function destroy(Test $test)
    {
        $test->delete();

        return redirect()->route('admin.tests.index')
            ->with('message', 'Test deleted.');
    }

    private function validateTest(Request $request, string $statusRule): array
    {
        $validated = $request->validate([
            'title'                          => ['required', 'string', 'max:255'],
            'description'                    => ['nullable', 'string'],
            'time_limit_minutes'              => ['required', 'integer', 'min:1', 'max:600'],
            'status'                          => ['required', "in:{$statusRule}"],
            'problems'                        => ['nullable', 'array'],
            'problems.*'                      => ['integer', 'exists:problems,id'],
            'points'                          => ['nullable', 'array'],
            'points.*'                        => ['nullable', 'integer', 'min:1', 'max:1000'],
            'new_problems'                    => ['nullable', 'array'],
            'new_problems.*.title'            => ['required', 'string', 'max:255'],
            'new_problems.*.description'      => ['required', 'string'],
            'new_problems.*.difficulty'       => ['required', 'in:easy,medium,hard'],
            'new_problems.*.category'         => ['nullable', 'string', 'max:60'],
            'new_problems.*.solution_code'    => ['nullable', 'string'],
            'new_problems.*.points'           => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        if (empty($validated['problems'] ?? []) && empty($validated['new_problems'] ?? [])) {
            throw ValidationException::withMessages([
                'problems' => 'Select at least one problem from the bank or add a custom one.',
            ]);
        }

        return $validated;
    }

    private function applyProblems(Test $test, array $problemIds, array $pointsById, array $newProblems): void
    {
        $pivotData = [];
        $orderIndex = 1;

        foreach (array_values($problemIds) as $problemId) {
            $pivotData[$problemId] = [
                'order_index' => $orderIndex++,
                'points'      => (int) ($pointsById[$problemId] ?? 100),
            ];
        }

        foreach ($newProblems as $custom) {
            $problem = Problem::create([
                'order_index'   => (int) (Problem::max('order_index') ?? 0) + 1,
                'title'         => $custom['title'],
                'description'   => $custom['description'],
                'difficulty'    => $custom['difficulty'],
                'category'      => $custom['category'] ?: 'Custom',
                'solution_code' => $custom['solution_code'] ?? '',
            ]);

            $pivotData[$problem->id] = [
                'order_index' => $orderIndex++,
                'points'      => (int) ($custom['points'] ?? 100),
            ];
        }

        $test->problems()->sync($pivotData);
    }
}
