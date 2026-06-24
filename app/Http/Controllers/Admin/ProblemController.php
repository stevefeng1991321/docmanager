<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Problem;

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
}
