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
        $snippets     = [];
        $indexMissing = false;

        if ($query !== '') {
            if ($mode === 'ai') {
                [$results, $total, $scores, $indexMissing] = $this->aiSearch(
                    $request, $query, $type, $categoryId, $dateFrom, $dateTo
                );
            } elseif ($mode === 'hybrid') {
                [$results, $total, $scores, $indexMissing] = $this->hybridSearch(
                    $request, $query, $type, $categoryId, $dateFrom, $dateTo
                );
            } else {
                [$results, $total] = $this->keywordSearch(
                    $request, $query, $type, $categoryId, $dateFrom, $dateTo, $sort
                );
            }

            if ($results->isNotEmpty()) {
                $snippets = $this->buildSnippets($results->pluck('id')->toArray(), $query);
            }

            SearchLog::create([
                'user_id'       => auth()->id(),
                'query'         => $query,
                'results_count' => $total,
                'search_type'   => match($mode) {
                    'ai'     => 'semantic',
                    'hybrid' => 'hybrid',
                    default  => 'keyword',
                },
            ]);
        }

        $categories    = Category::orderBy('name')->get();
        $savedSearches = auth()->user()->savedSearches()->orderByDesc('created_at')->take(5)->get();

        return view('search.index', compact(
            'query', 'mode', 'results', 'total', 'scores', 'snippets', 'indexMissing',
            'savedSearches', 'categories', 'type', 'categoryId', 'dateFrom', 'dateTo', 'sort'
        ));
    }

    // ── Autocomplete suggestions ─────────────────────────────────────────────

    public function suggest(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = Resource::published()
            ->where('title', 'like', '%' . $q . '%')
            ->orderByDesc('download_count')
            ->limit(8)
            ->pluck('title');

        return response()->json($results);
    }

    // ── Keyword search ────────────────────────────────────────────────────────

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

        if ($sort === 'relevance' && strlen($query) >= 3) {
            // Use actual MySQL FULLTEXT relevance score (computed in ORDER BY — no extra SELECT needed)
            $builder = $builder->orderByRaw(
                'MATCH(title, description, content) AGAINST(? IN BOOLEAN MODE) DESC',
                ["+{$escaped}*"]
            );
        } elseif ($sort === 'relevance') {
            $builder = $builder->orderByDesc('download_count');
        } else {
            $builder = $builder->sorted($sort);
        }

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

        $idf         = $tfidf->loadIdf();
        $queryTokens = $tfidf->tokenize($query);

        if (empty($queryTokens)) {
            return [collect(), 0, [], false];
        }

        $queryTf  = $tfidf->computeTf($queryTokens);
        $queryVec = $tfidf->computeTfidfVector($queryTf, $idf);

        if (empty($queryVec)) {
            return [collect(), 0, [], false];
        }

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

        $embeddings = ResourceEmbedding::where('model', 'tfidf-v1')
            ->whereIn('resource_id', $eligibleIds)
            ->pluck('embedding', 'resource_id')
            ->toArray();

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

        $page     = max(1, (int) $request->input('page', 1));
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

    // ── Hybrid search: FULLTEXT (40%) + TF-IDF (60%) ─────────────────────────

    private function hybridSearch(Request $request, string $query, ?string $type, ?string $categoryId, ?string $dateFrom, ?string $dateTo): array
    {
        $tfidf = app(TfidfService::class);

        if (!$tfidf->hasIndex()) {
            return [collect(), 0, [], true];
        }

        $escaped  = addslashes($query);
        $ftScores = [];

        // FULLTEXT pass — skip for very short queries (FULLTEXT needs >= 3 chars)
        if (strlen($query) >= 3) {
            $ftRows = Resource::published()
                ->selectRaw('id, MATCH(title, description, content) AGAINST(? IN BOOLEAN MODE) AS ft_score', ["+{$escaped}*"])
                ->whereRaw('MATCH(title, description, content) AGAINST(? IN BOOLEAN MODE)', ["+{$escaped}*"])
                ->when($type,       fn($q, $t) => $q->where('file_type', 'like', "%{$t}%"))
                ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
                ->when($dateFrom,   fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when($dateTo,     fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->get();

            $maxFt = (float) ($ftRows->max('ft_score') ?: 1.0);
            foreach ($ftRows as $row) {
                $ftScores[$row->id] = (float) $row->ft_score / $maxFt;
            }
        }

        // TF-IDF pass
        $idf         = $tfidf->loadIdf();
        $queryTokens = $tfidf->tokenize($query);
        $tfidfScores = [];

        if (!empty($queryTokens)) {
            $queryVec = $tfidf->computeTfidfVector($tfidf->computeTf($queryTokens), $idf);

            if (!empty($queryVec)) {
                $eligibleIds = Resource::published()
                    ->when($type,       fn($q, $t) => $q->where('file_type', 'like', "%{$t}%"))
                    ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
                    ->when($dateFrom,   fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                    ->when($dateTo,     fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                    ->pluck('id')
                    ->toArray();

                foreach (ResourceEmbedding::where('model', 'tfidf-v1')
                    ->whereIn('resource_id', $eligibleIds)
                    ->pluck('embedding', 'resource_id') as $resourceId => $vector) {
                    $sim = $tfidf->cosineSimilarity($queryVec, $vector);
                    if ($sim > 0.0) {
                        $tfidfScores[$resourceId] = $sim;
                    }
                }
            }
        }

        // Union of both result sets; combine with 40 / 60 weighting
        $allIds = array_unique(array_merge(array_keys($ftScores), array_keys($tfidfScores)));

        if (empty($allIds)) {
            return [collect(), 0, [], false];
        }

        $combinedScores = [];
        foreach ($allIds as $id) {
            $combinedScores[$id] = 0.4 * ($ftScores[$id] ?? 0.0) + 0.6 * ($tfidfScores[$id] ?? 0.0);
        }

        arsort($combinedScores);
        $total = count($combinedScores);

        $page     = max(1, (int) $request->input('page', 1));
        $pagedIds = array_keys(array_slice($combinedScores, ($page - 1) * self::PER_PAGE, self::PER_PAGE, true));

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

        return [$paginator, $total, $combinedScores, false];
    }

    // ── Snippet helpers ───────────────────────────────────────────────────────

    private function buildSnippets(array $ids, string $query): array
    {
        if (empty($ids)) {
            return [];
        }

        $rows = Resource::whereIn('id', $ids)
            ->selectRaw('id, description, SUBSTRING(content, 1, 600) AS content_preview')
            ->get()
            ->keyBy('id');

        $snippets = [];
        foreach ($ids as $id) {
            $row = $rows[$id] ?? null;
            if (!$row) {
                continue;
            }
            // Prefer description when it's meaningfully long; fall back to extracted content
            $source = (mb_strlen((string) $row->description) > 20)
                ? (string) $row->description
                : (string) ($row->content_preview ?? '');
            $snippets[$id] = $this->extractSnippet($source, $query);
        }

        return $snippets;
    }

    private function extractSnippet(string $text, string $query, int $maxLen = 220): string
    {
        $text = preg_replace('/\s+/', ' ', trim(strip_tags($text)));
        if ($text === '') {
            return '';
        }

        // Find best anchor: full phrase first, then first long query word
        $pos = mb_stripos($text, $query);
        if ($pos === false) {
            foreach (preg_split('/\s+/', $query) as $word) {
                if (mb_strlen($word) >= 3) {
                    $p = mb_stripos($text, $word);
                    if ($p !== false) {
                        $pos = $p;
                        break;
                    }
                }
            }
        }

        // Centre window ~60 chars before the match
        $start = max(0, ($pos ?? 0) - 60);
        if ($start > 0) {
            $spacePos = mb_strpos($text, ' ', $start);
            $start    = ($spacePos !== false) ? $spacePos + 1 : $start;
        }

        $snippet = mb_substr($text, $start, $maxLen);
        $textLen = mb_strlen($text);

        // Trim to last word boundary if we're not at the end
        if ($start + $maxLen < $textLen) {
            $lastSpace = mb_strrpos($snippet, ' ');
            if ($lastSpace !== false) {
                $snippet = mb_substr($snippet, 0, $lastSpace);
            }
            $snippet .= '…';
        }
        if ($start > 0) {
            $snippet = '…' . $snippet;
        }

        return $snippet;
    }
}
