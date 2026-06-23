<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Resource;
use App\Models\SavedSearch;
use App\Models\SearchLog;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query      = trim($request->input('q', ''));
        $type       = $request->input('type');
        $categoryId = $request->input('category_id');
        $dateFrom   = $request->input('date_from');
        $dateTo     = $request->input('date_to');
        $sort       = $request->input('sort', 'relevance');

        $results = collect();
        $total   = 0;

        if ($query !== '') {
            $escaped = addslashes($query);

            $builder = Resource::published()
                ->with(['category', 'tags'])
                ->withAvg('ratings', 'rating')
                ->when(strlen($query) >= 3, function ($q) use ($escaped) {
                    $q->whereRaw(
                        "MATCH(title, description, content) AGAINST (? IN BOOLEAN MODE)",
                        ["+{$escaped}*"]
                    );
                }, function ($q) use ($query) {
                    $q->where(function ($inner) use ($query) {
                        $inner->where('title', 'like', "%{$query}%")
                              ->orWhere('description', 'like', "%{$query}%");
                    });
                })
                ->when($type,       fn($q, $t) => $q->where('file_type', 'like', "%{$t}%"))
                ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
                ->when($dateFrom,   fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when($dateTo,     fn($q, $d) => $q->whereDate('created_at', '<=', $d));

            $builder = match($sort) {
                'date_desc'  => $builder->orderByDesc('created_at'),
                'date_asc'   => $builder->orderBy('created_at'),
                'name_asc'   => $builder->orderBy('title'),
                'name_desc'  => $builder->orderByDesc('title'),
                'size_desc'  => $builder->orderByDesc('file_size'),
                'downloads'  => $builder->orderByDesc('download_count'),
                default      => $builder->orderByDesc('download_count'),
            };

            $results = $builder->paginate(15)->withQueryString();
            $total   = $results->total();

            SearchLog::create([
                'user_id'       => auth()->id(),
                'query'         => $query,
                'results_count' => $total,
            ]);
        }

        $categories  = Category::orderBy('name')->get();
        $savedSearches = auth()->user()->savedSearches()->orderByDesc('created_at')->take(5)->get();

        return view('search.index', compact(
            'query', 'results', 'total', 'savedSearches', 'categories',
            'type', 'categoryId', 'dateFrom', 'dateTo', 'sort'
        ));
    }
}
