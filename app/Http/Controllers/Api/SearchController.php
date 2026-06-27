<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Models\Resource;
use App\Models\ResourceEmbedding;
use App\Models\SearchLog;
use App\Services\TfidfService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends Controller
{
    public function __construct(private TfidfService $tfidf) {}

    public function __invoke(Request $request)
    {
        $query      = trim($request->input('q', ''));
        $type       = $request->input('type');
        $categoryId = $request->input('category_id');
        $dateFrom   = $request->input('date_from');
        $dateTo     = $request->input('date_to');
        $sort       = $request->input('sort', 'relevance');
        $perPage    = min((int) $request->input('per_page', config('pagination.api_default_per_page')), config('pagination.api_max_per_page'));

        $usesSemantic = $query !== '' && strlen($query) >= 3 && $this->tfidf->hasIndex();

        [$results, $searchMode] = $usesSemantic
            ? $this->semanticSearch($request, $query, $type, $categoryId, $dateFrom, $dateTo, $perPage)
            : [$this->fulltextSearch($query, $type, $categoryId, $dateFrom, $dateTo, $sort, $perPage), 'fulltext'];

        if ($query !== '') {
            SearchLog::create([
                'user_id'       => $request->user()?->id,
                'query'         => $query,
                'results_count' => $results->total(),
            ]);
        }

        return DocumentResource::collection($results)->additional([
            'meta' => [
                'query'       => $query,
                'total'       => $results->total(),
                'filters'     => compact('type', 'categoryId', 'dateFrom', 'dateTo'),
                'sort'        => $sort,
                'search_mode' => $searchMode,
            ],
        ]);
    }

    /**
     * TF-IDF semantic search with FULLTEXT boost for exact term matches.
     * Falls back to fulltextSearch() when the query vector is empty or yields no results.
     */
    private function semanticSearch(
        Request $request,
        string $query,
        ?string $type,
        ?string $categoryId,
        ?string $dateFrom,
        ?string $dateTo,
        int $perPage
    ): array {
        $idf      = $this->tfidf->loadIdf();
        $tokens   = $this->tfidf->tokenize($query);
        $queryVec = $this->tfidf->computeTfidfVector($this->tfidf->computeTf($tokens), $idf);

        if (empty($queryVec)) {
            return [$this->fulltextSearch($query, $type, $categoryId, $dateFrom, $dateTo, 'relevance', $perPage), 'fulltext'];
        }

        // Score every TF-IDF indexed embedding via cosine similarity
        $scores = [];
        ResourceEmbedding::where('model', 'tfidf-v1')
            ->select('resource_id', 'embedding')
            ->get()
            ->each(function ($emb) use ($queryVec, &$scores) {
                $sim = $this->tfidf->cosineSimilarity($queryVec, $emb->embedding);
                if ($sim >= 0.05) {
                    $scores[$emb->resource_id] = $sim;
                }
            });

        if (empty($scores)) {
            return [$this->fulltextSearch($query, $type, $categoryId, $dateFrom, $dateTo, 'relevance', $perPage), 'fulltext'];
        }

        // Boost resources that also satisfy a FULLTEXT match (exact term present)
        $escaped     = addslashes($query);
        $fulltextHit = Resource::published()
            ->whereIn('id', array_keys($scores))
            ->whereRaw("MATCH(title, description, content) AGAINST (? IN BOOLEAN MODE)", ["+{$escaped}*"])
            ->pluck('id')
            ->all();

        foreach ($fulltextHit as $id) {
            $scores[$id] = min(1.0, $scores[$id] * 1.4);
        }

        arsort($scores);
        $rankedIds = array_keys($scores);

        // Apply declared filters, preserving TF-IDF relevance order
        $filteredSet = Resource::published()
            ->whereIn('id', $rankedIds)
            ->when($type,       fn($q) => $q->where('file_type', 'like', "%{$type}%"))
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($dateFrom,   fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo,     fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->pluck('id')
            ->flip()
            ->all();

        $orderedIds = array_values(array_filter($rankedIds, fn($id) => isset($filteredSet[$id])));

        if (empty($orderedIds)) {
            return [$this->fulltextSearch($query, $type, $categoryId, $dateFrom, $dateTo, 'relevance', $perPage), 'fulltext'];
        }

        // Paginate over the relevance-ordered ID list
        $page    = LengthAwarePaginator::resolveCurrentPage();
        $pageIds = array_slice($orderedIds, ($page - 1) * $perPage, $perPage);

        $resourceMap = Resource::published()
            ->with(['category', 'tags'])
            ->withAvg('ratings', 'rating')
            ->whereIn('id', $pageIds)
            ->get()
            ->keyBy('id');

        $items = collect($pageIds)
            ->map(fn($id) => $resourceMap->get($id))
            ->filter()
            ->values();

        return [
            new LengthAwarePaginator($items, count($orderedIds), $perPage, $page, [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]),
            'semantic',
        ];
    }

    private function fulltextSearch(
        string $query,
        ?string $type,
        ?string $categoryId,
        ?string $dateFrom,
        ?string $dateTo,
        string $sort,
        int $perPage
    ) {
        $builder = Resource::published()
            ->with(['category', 'tags'])
            ->withAvg('ratings', 'rating')
            ->when($type,       fn($q, $t) => $q->where('file_type', 'like', "%{$t}%"))
            ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
            ->when($dateFrom,   fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($dateTo,     fn($q, $d) => $q->whereDate('created_at', '<=', $d));

        if ($query !== '') {
            $escaped = addslashes($query);
            if (strlen($query) >= 3) {
                $builder->whereRaw(
                    "MATCH(title, description, content) AGAINST (? IN BOOLEAN MODE)",
                    ["+{$escaped}*"]
                );
            } else {
                $builder->where(fn($q) => $q
                    ->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                );
            }
        }

        return $sort === 'relevance'
            ? $builder->orderByDesc('download_count')->paginate($perPage)
            : $builder->sorted($sort)->paginate($perPage);
    }
}
