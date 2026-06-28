<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScienceTechTrend;
use Illuminate\Http\Request;

class ScienceTechTrendController extends Controller
{
    public function index(Request $request)
    {
        $q      = trim($request->get('q', ''));
        $status = $request->get('status', '');
        $year   = $request->get('year', '');
        $sort   = $request->get('sort', 'year_desc');

        $query = ScienceTechTrend::query();

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title',   'like', "%{$q}%")
                    ->orWhere('summary', 'like', "%{$q}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($year) {
            $query->where('year', $year);
        }

        match ($sort) {
            'year_asc'    => $query->orderBy('year')->orderBy('id'),
            'title_asc'   => $query->orderBy('title'),
            'title_desc'  => $query->orderByDesc('title'),
            'newest'      => $query->orderByDesc('created_at'),
            default       => $query->orderByDesc('year')->orderBy('id'),
        };

        $trends = $query->paginate(20)->withQueryString();
        $years  = ScienceTechTrend::distinct()->orderByDesc('year')->pluck('year');

        return view('admin.science-tech.index', compact('trends', 'q', 'status', 'year', 'sort', 'years'));
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

        $trend = ScienceTechTrend::create($validated);

        return redirect()->route('admin.science-tech.edit', $trend)
            ->with('message', 'Trend created. You can now add media below.');
    }

    public function show(ScienceTechTrend $trend)
    {
        $trend->load('media');

        return view('admin.science-tech.show', compact('trend'));
    }

    public function edit(ScienceTechTrend $trend)
    {
        $trend->load('media');

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
