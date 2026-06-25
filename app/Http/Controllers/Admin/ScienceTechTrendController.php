<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScienceTechTrend;
use Illuminate\Http\Request;

class ScienceTechTrendController extends Controller
{
    public function index()
    {
        $byYear = ScienceTechTrend::orderByDesc('year')->orderBy('id')
            ->get()
            ->groupBy('year');

        return view('admin.science-tech.index', compact('byYear'));
    }

    public function create()
    {
        return view('admin.science-tech.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'summary' => ['nullable', 'string'],
            'status'  => ['required', 'in:draft,published,archived'],
            'year'    => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        ScienceTechTrend::create($validated);

        return redirect()->route('admin.science-tech.index')
            ->with('message', 'Trend created.');
    }

    public function show(ScienceTechTrend $trend)
    {
        return view('admin.science-tech.show', compact('trend'));
    }

    public function edit(ScienceTechTrend $trend)
    {
        return view('admin.science-tech.edit', compact('trend'));
    }

    public function update(Request $request, ScienceTechTrend $trend)
    {
        $validated = $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'summary' => ['nullable', 'string'],
            'status'  => ['required', 'in:draft,published,archived'],
            'year'    => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $trend->update($validated);

        return redirect()->route('admin.science-tech.index')
            ->with('message', 'Trend updated.');
    }

    public function destroy(ScienceTechTrend $trend)
    {
        $trend->delete();

        return redirect()->route('admin.science-tech.index')
            ->with('message', 'Trend deleted.');
    }
}
