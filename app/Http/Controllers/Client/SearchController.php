<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Resource;
use App\Models\ResourceEmbedding;
use App\Models\SavedSearch;
use App\Models\SearchLog;
use App\Services\TfidfService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends Controller
{
    private const PER_PAGE = 15;

    public function index(Request $request)
    {
        $query      = trim($request->input('q', ''));
        $mode       = $request->input('mode', 'keyword');
        $type       = $request->input('type');
        $categoryId = $request->input('category_id');
        $dateFrom   = $request->input('date_from');
        $dateTo     = $request->input('date_to');
        $sort       = $request->input('sort', 'relevance');

        $results      = collect();
        $total        = 0;
        $scores       = [];
        $indexMissing = false;

        if ($query !== '') {
            if ($mode === 'ai') {
                [$results, $total, $scores, $indexMissing] = $this->aiSearch(
                    $request, $query, $type, $categoryId, $dateFrom, $dateTo
                );
            } else {
                [$results, $total] = $this->keywordSearch(
                    $request, $query, $type, $categoryId, $dateFrom, $dateTo, $sort
                );
            }

            SearchLog::create([
                'user_id'       => auth()->id(),
                'query'         => $query,
                'results_count' => $total,
                'search_type'   => $mode === 'ai' ? 'semantic' : 'keyword',
            ]);
        }

        $categories    = Category::orderBy('name')->get();
        $savedSearches = auth()->user()->savedSearches()->orderByDesc('created_at')->take(5)->get();

        return view('search.index', compact(
            'query', 'mode', 'results', 'total', 'scores', 'indexMissing',
            'savedSearches', 'categories', 'type', 'categoryId', 'dateFrom', 'dateTo', 'sort'
        ));
    }

    // ── Keyword search (existing MySQL FULLTEXT logic) ────────────────────────

    private function keywordSearch(Request $request, string $query, ?string $type, ?string $categoryId, ?string $dateFrom, ?string $dateTo, string $sort): array
    {
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

        $results = $builder->paginate(self::PER_PAGE)->withQueryString();

        return [$results, $results->total()];
    }

    // ── TF-IDF semantic search ────────────────────────────────────────────────

    private function aiSearch(Request $request, string $query, ?string $type, ?string $categoryId, ?string $dateFrom, ?string $dateTo): array
    {
        $tfidf = app(TfidfService::class);

        if (!$tfidf->hasIndex()) {
            return [collect(), 0, [], true];
        }

        $idf = $tfidf->loadIdf();

        $queryTokens = $tfidf->tokenize($query);
        if (empty($queryTokens)) {
            return [collect(), 0, [], false];
        }

        $queryTf  = $tfidf->computeTf($queryTokens);
        $queryVec = $tfidf->computeTfidfVector($queryTf, $idf);

        if (empty($queryVec)) {
            return [collect(), 0, [], false];
        }

        // Eligible resource IDs (published + optional filters)
        $eligibleIds = Resource::published()
            ->when($type,       fn($q, $t) => $q->where('file_type', 'like', "%{$t}%"))
            ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
            ->when($dateFrom,   fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($dateTo,     fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->pluck('id')
            ->toArray();

        if (empty($eligibleIds)) {
            return [collect(), 0, [], false];
        }

        // Load TF-IDF vectors for eligible docs
        $embeddings = ResourceEmbedding::where('model', 'tfidf-v1')
            ->whereIn('resource_id', $eligibleIds)
            ->pluck('embedding', 'resource_id')
            ->toArray();

        // Compute cosine similarity for each doc
        $scores = [];
        foreach ($embeddings as $resourceId => $vector) {
            $sim = $tfidf->cosineSimilarity($queryVec, $vector);
            if ($sim > 0.0) {
                $scores[$resourceId] = $sim;
            }
        }

        arsort($scores);
        $total = count($scores);

        if ($total === 0) {
            return [collect(), 0, [], false];
        }

        // Manual pagination
        $page    = max(1, (int) $request->input('page', 1));
        $pagedIds = array_keys(array_slice($scores, ($page - 1) * self::PER_PAGE, self::PER_PAGE, true));

        $resourcesById = Resource::whereIn('id', $pagedIds)
            ->with(['category', 'tags'])
            ->withAvg('ratings', 'rating')
            ->get()
            ->keyBy('id');

        $orderedResults = collect($pagedIds)
            ->map(fn($id) => $resourcesById[$id] ?? null)
            ->filter()
            ->values();

        $paginator = new LengthAwarePaginator(
            $orderedResults,
            $total,
            self::PER_PAGE,
            $page,
            ['path' => $request->url(), 'query' => $request->except('page')]
        );

        return [$paginator, $total, $scores, false];
    }
}
