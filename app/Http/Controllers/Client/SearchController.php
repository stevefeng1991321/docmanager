<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\SavedSearch;
use App\Models\SearchLog;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->input('q', ''));
        $results = collect();
        $total = 0;

        if ($query !== '') {
            $escaped = addslashes($query);

            $results = Resource::published()
                ->with(['category', 'tags'])
                ->when(strlen($query) >= 3, function ($q) use ($escaped) {
                    // MySQL FULLTEXT search on indexed columns
                    $q->whereRaw(
                        "MATCH(title, description, content) AGAINST (? IN BOOLEAN MODE)",
                        ["+{$escaped}*"]
                    );
                }, function ($q) use ($query) {
                    // Fallback: LIKE search for very short queries
                    $q->where(function ($inner) use ($query) {
                        $inner->where('title', 'like', "%{$query}%")
                              ->orWhere('description', 'like', "%{$query}%");
                    });
                })
                ->orderByDesc('download_count')
                ->paginate(15)
                ->withQueryString();

            $total = $results->total();

            SearchLog::create([
                'user_id'       => auth()->id(),
                'query'         => $query,
                'results_count' => $total,
            ]);
        }

        $savedSearches = auth()->user()
            ->savedSearches()
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('search.index', compact('query', 'results', 'total', 'savedSearches'));
    }
}
