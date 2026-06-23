<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchLog;
use Illuminate\Support\Facades\DB;

class SearchIndexController extends Controller
{
    public function index()
    {
        $topQueries = SearchLog::selectRaw('query, COUNT(*) as count')
            ->groupBy('query')
            ->orderByDesc('count')
            ->take(20)
            ->get();

        return view('admin.search.index', compact('topQueries'));
    }

    public function reindex()
    {
        // Dispatch re-index job (implemented in Phase 2)
        return back()->with('message', 'Re-index queued.');
    }
}
