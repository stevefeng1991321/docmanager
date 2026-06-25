<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ScienceTechTrend;
use Illuminate\Http\Request;

class ScienceTechController extends Controller
{
    public function index(Request $request)
    {
        $q    = trim($request->get('q', ''));
        $year = $request->get('year');

        $trends = ScienceTechTrend::where('status', 'published')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title',   'like', "%{$q}%")
                        ->orWhere('summary', 'like', "%{$q}%")
                        ->orWhere('content', 'like', "%{$q}%");
                });
            })
            ->when($year, fn($query) => $query->where('year', $year))
            ->orderByDesc('year')
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString();

        $years = ScienceTechTrend::where('status', 'published')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('science-tech.index', compact('trends', 'q', 'year', 'years'));
    }

    public function show(ScienceTechTrend $trend)
    {
        if ($trend->status !== 'published') {
            abort(404);
        }

        return view('science-tech.show', compact('trend'));
    }
}
