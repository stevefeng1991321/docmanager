<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BasicKnowledgeTrend;
use App\Models\Category;
use App\Models\Resource;
use App\Models\ResourceEmbedding;
use App\Models\SavedSearch;
use App\Models\SearchLog;
use App\Services\TfidfService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SearchController extends Controller
{
    private const PER_PAGE   = 15;
    private const DOC_PREFIX = 'doc_';
    private const KB_PREFIX  = 'kb_';

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
                [$results, $total, $scores] = $this->keywordSearch(
                    $request, $query, $type, $categoryId, $dateFrom, $dateTo, $sort
                );
            }

            if ($results->isNotEmpty()) {
                $snippets = $this->buildSnippets($results, $query);
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

        $docTitles = Resource::published()
            ->where('title', 'like', '%' . $q . '%')
            ->orderByDesc('download_count')
            ->limit(5)
            ->pluck('title');

        $kbTitles = BasicKnowledgeTrend::where('status', 'published')
            ->where('title', 'like', '%' . $q . '%')
            ->latest()
            ->limit(3)
            ->pluck('title');

        return response()->json($docTitles->concat($kbTitles)->unique()->take(8)->values());
    }

    // ── Keyword search ────────────────────────────────────────────────────────

    private function keywordSearch(Request $request, string $query, ?string $type, ?string $categoryId, ?string $dateFrom, ?string $dateTo, string $sort): array
    {
        $escaped     = addslashes($query);
        $useFullText = mb_strlen($query) >= 3;
        $scores      = [];

        // --- Documents: collect IDs + FT scores ---
        if ($useFullText) {
            $docRows = Resource::published()
                ->selectRaw('id, MATCH(title, description, content) AGAINST(? IN BOOLEAN MODE) AS ft_score', ["+{$escaped}*"])
                ->whereRaw('MATCH(title, description, content) AGAINST(? IN BOOLEAN MODE)', ["+{$escaped}*"])
                ->when($type,       fn($q, $t) => $q->where('file_type', 'like', "%{$t}%"))
                ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
                ->when($dateFrom,   fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when($dateTo,     fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->get();
            $maxDocFt = max(1.0, (float)($docRows->max('ft_score') ?? 1.0));
            foreach ($docRows as $row) {
                $scores[self::DOC_PREFIX . $row->id] = (float)$row->ft_score / $maxDocFt;
            }
        } else {
            Resource::published()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->when($type,       fn($q, $t) => $q->where('file_type', 'like', "%{$t}%"))
                ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
                ->when($dateFrom,   fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when($dateTo,     fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->pluck('id')
                ->each(fn($id) => $scores[self::DOC_PREFIX . $id] = 0.5);
        }

        // --- Knowledge entries (skipped when file-type filter is active) ---
        if (!$type) {
            if ($useFullText) {
                $kbRows = BasicKnowledgeTrend::where('status', 'published')
                    ->selectRaw('id, MATCH(title, summary, content) AGAINST(? IN BOOLEAN MODE) AS ft_score', ["+{$escaped}*"])
                    ->whereRaw('MATCH(title, summary, content) AGAINST(? IN BOOLEAN MODE)', ["+{$escaped}*"])
                    ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
                    ->when($dateFrom,   fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                    ->when($dateTo,     fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                    ->get();
                $maxKbFt = max(1.0, (float)($kbRows->max('ft_score') ?? 1.0));
                foreach ($kbRows as $row) {
                    $scores[self::KB_PREFIX . $row->id] = (float)$row->ft_score / $maxKbFt;
                }
            } else {
                BasicKnowledgeTrend::where('status', 'published')
                    ->where(function ($q) use ($query) {
                        $q->where('title',   'like', "%{$query}%")
                          ->orWhere('summary', 'like', "%{$query}%")
                          ->orWhere('content', 'like', "%{$query}%");
                    })
                    ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
                    ->when($dateFrom,   fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                    ->when($dateTo,     fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                    ->pluck('id')
                    ->each(fn($id) => $scores[self::KB_PREFIX . $id] = 0.5);
            }
        }

        if (empty($scores)) {
            return [collect(), 0, []];
        }

        $total = count($scores);
        $page  = max(1, (int)$request->input('page', 1));

        // For relevance sort: slice the already-sorted score array for the page
        // For other sorts: load all records, sort in PHP, then slice
        if ($sort === 'relevance') {
            arsort($scores);
            $pagedKeys = array_keys(array_slice($scores, ($page - 1) * self::PER_PAGE, self::PER_PAGE, true));
            $paged     = $this->loadResultsByKeys($pagedKeys);
        } else {
            $allRecords = $this->loadResultsByKeys(array_keys($scores));
            $sorted     = match($sort) {
                'name_asc'  => $allRecords->sortBy(fn($r) => mb_strtolower($r->title))->values(),
                'name_desc' => $allRecords->sortByDesc(fn($r) => mb_strtolower($r->title))->values(),
                'date_asc'  => $allRecords->sortBy('created_at')->values(),
                'date_desc' => $allRecords->sortByDesc('created_at')->values(),
                'downloads' => $allRecords->sortByDesc(fn($r) => $r->download_count ?? -1)->values(),
                'size_desc' => $allRecords->sortByDesc(fn($r) => $r->file_size ?? -1)->values(),
                default     => $allRecords->sortByDesc(fn($r) => $scores[$r->_key] ?? 0)->values(),
            };
            $paged = $sorted->forPage($page, self::PER_PAGE)->values();
        }

        $paginator = new LengthAwarePaginator(
            $paged, $total, self::PER_PAGE, $page,
            ['path' => $request->url(), 'query' => $request->except('page')]
        );

        return [$paginator, $total, $scores];
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

        $scores = [];

        // --- Documents: cosine similarity against TF-IDF embeddings ---
        $eligibleDocIds = Resource::published()
            ->when($type,       fn($q, $t) => $q->where('file_type', 'like', "%{$t}%"))
            ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
            ->when($dateFrom,   fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($dateTo,     fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->pluck('id')
            ->toArray();

        foreach (ResourceEmbedding::where('model', 'tfidf-v1')
            ->whereIn('resource_id', $eligibleDocIds)
            ->pluck('embedding', 'resource_id') as $resourceId => $vector) {
            $sim = $tfidf->cosineSimilarity($queryVec, $vector);
            if ($sim > 0.0) {
                $scores[self::DOC_PREFIX . $resourceId] = $sim;
            }
        }

        // --- Knowledge entries: FULLTEXT scoring (no TF-IDF embeddings for KB) ---
        if (!$type && mb_strlen($query) >= 3) {
            $escaped = addslashes($query);
            $kbRows  = BasicKnowledgeTrend::where('status', 'published')
                ->selectRaw('id, MATCH(title, summary, content) AGAINST(? IN BOOLEAN MODE) AS ft_score', ["+{$escaped}*"])
                ->whereRaw('MATCH(title, summary, content) AGAINST(? IN BOOLEAN MODE)', ["+{$escaped}*"])
                ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
                ->when($dateFrom,   fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when($dateTo,     fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->get();
            $maxKbFt = max(1.0, (float)($kbRows->max('ft_score') ?? 1.0));
            foreach ($kbRows as $row) {
                $sim = (float)$row->ft_score / $maxKbFt;
                if ($sim > 0.0) {
                    $scores[self::KB_PREFIX . $row->id] = $sim;
                }
            }
        }

        arsort($scores);
        $total = count($scores);

        if ($total === 0) {
            return [collect(), 0, [], false];
        }

        $page      = max(1, (int)$request->input('page', 1));
        $pagedKeys = array_keys(array_slice($scores, ($page - 1) * self::PER_PAGE, self::PER_PAGE, true));
        $paged     = $this->loadResultsByKeys($pagedKeys);

        $paginator = new LengthAwarePaginator(
            $paged, $total, self::PER_PAGE, $page,
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

        // --- Documents: FULLTEXT pass ---
        if (mb_strlen($query) >= 3) {
            $ftRows = Resource::published()
                ->selectRaw('id, MATCH(title, description, content) AGAINST(? IN BOOLEAN MODE) AS ft_score', ["+{$escaped}*"])
                ->whereRaw('MATCH(title, description, content) AGAINST(? IN BOOLEAN MODE)', ["+{$escaped}*"])
                ->when($type,       fn($q, $t) => $q->where('file_type', 'like', "%{$t}%"))
                ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
                ->when($dateFrom,   fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when($dateTo,     fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->get();
            $maxFt = max(1.0, (float)($ftRows->max('ft_score') ?? 1.0));
            foreach ($ftRows as $row) {
                $ftScores[self::DOC_PREFIX . $row->id] = (float)$row->ft_score / $maxFt;
            }
        }

        // --- Documents: TF-IDF pass ---
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
                        $tfidfScores[self::DOC_PREFIX . $resourceId] = $sim;
                    }
                }
            }
        }

        // --- Document combined scores: 40% FT + 60% TF-IDF ---
        $combinedScores = [];
        foreach (array_unique(array_merge(array_keys($ftScores), array_keys($tfidfScores))) as $key) {
            $combinedScores[$key] = 0.4 * ($ftScores[$key] ?? 0.0) + 0.6 * ($tfidfScores[$key] ?? 0.0);
        }

        // --- Knowledge entries: FULLTEXT only ---
        if (!$type && mb_strlen($query) >= 3) {
            $kbRows = BasicKnowledgeTrend::where('status', 'published')
                ->selectRaw('id, MATCH(title, summary, content) AGAINST(? IN BOOLEAN MODE) AS ft_score', ["+{$escaped}*"])
                ->whereRaw('MATCH(title, summary, content) AGAINST(? IN BOOLEAN MODE)', ["+{$escaped}*"])
                ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
                ->when($dateFrom,   fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when($dateTo,     fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->get();
            $maxKbFt = max(1.0, (float)($kbRows->max('ft_score') ?? 1.0));
            foreach ($kbRows as $row) {
                $sim = (float)$row->ft_score / $maxKbFt;
                if ($sim > 0.0) {
                    $combinedScores[self::KB_PREFIX . $row->id] = $sim;
                }
            }
        }

        arsort($combinedScores);
        $total = count($combinedScores);

        if ($total === 0) {
            return [collect(), 0, [], false];
        }

        $page      = max(1, (int)$request->input('page', 1));
        $pagedKeys = array_keys(array_slice($combinedScores, ($page - 1) * self::PER_PAGE, self::PER_PAGE, true));
        $paged     = $this->loadResultsByKeys($pagedKeys);

        $paginator = new LengthAwarePaginator(
            $paged, $total, self::PER_PAGE, $page,
            ['path' => $request->url(), 'query' => $request->except('page')]
        );

        return [$paginator, $total, $combinedScores, false];
    }

    // ── Load mixed doc + knowledge records by prefixed keys ──────────────────

    private function loadResultsByKeys(array $keys): Collection
    {
        $docIds = [];
        $kbIds  = [];

        foreach ($keys as $key) {
            if (str_starts_with($key, self::DOC_PREFIX)) {
                $docIds[] = (int)substr($key, strlen(self::DOC_PREFIX));
            } else {
                $kbIds[] = (int)substr($key, strlen(self::KB_PREFIX));
            }
        }

        $docsById = Resource::whereIn('id', $docIds)
            ->with(['category', 'tags'])
            ->withAvg('ratings', 'rating')
            ->get()
            ->keyBy('id');

        $kbById = BasicKnowledgeTrend::whereIn('id', $kbIds)
            ->with('category')
            ->get()
            ->keyBy('id');

        return collect($keys)->map(function (string $key) use ($docsById, $kbById) {
            if (str_starts_with($key, self::DOC_PREFIX)) {
                $rec = $docsById[(int)substr($key, strlen(self::DOC_PREFIX))] ?? null;
                if ($rec) { $rec->_type = 'document'; $rec->_key = $key; }
            } else {
                $rec = $kbById[(int)substr($key, strlen(self::KB_PREFIX))] ?? null;
                if ($rec) { $rec->_type = 'knowledge'; $rec->_key = $key; }
            }
            return $rec ?? null;
        })->filter()->values();
    }

    // ── Snippet helpers ───────────────────────────────────────────────────────

    private function buildSnippets($results, string $query): array
    {
        $snippets = [];
        $results  = collect($results);

        $docIds = $results->where('_type', 'document')->pluck('id')->toArray();
        $kbIds  = $results->where('_type', 'knowledge')->pluck('id')->toArray();

        if (!empty($docIds)) {
            Resource::whereIn('id', $docIds)
                ->selectRaw('id, description, SUBSTRING(content, 1, 600) AS content_preview')
                ->get()
                ->each(function ($row) use ($query, &$snippets) {
                    $source = mb_strlen((string)$row->description) > 20
                        ? (string)$row->description
                        : (string)($row->content_preview ?? '');
                    $snippets[self::DOC_PREFIX . $row->id] = $this->extractSnippet($source, $query);
                });
        }

        if (!empty($kbIds)) {
            BasicKnowledgeTrend::whereIn('id', $kbIds)
                ->selectRaw('id, summary, SUBSTRING(content, 1, 600) AS content_preview')
                ->get()
                ->each(function ($row) use ($query, &$snippets) {
                    $source = mb_strlen((string)$row->summary) > 20
                        ? (string)$row->summary
                        : (string)($row->content_preview ?? '');
                    $snippets[self::KB_PREFIX . $row->id] = $this->extractSnippet($source, $query);
                });
        }

        return $snippets;
    }

    private function extractSnippet(string $text, string $query, int $maxLen = 220): string
    {
        $text = preg_replace('/\s+/', ' ', trim(strip_tags($text)));
        if ($text === '') {
            return '';
        }

        $pos = mb_stripos($text, $query);
        if ($pos === false) {
            foreach (preg_split('/\s+/', $query) as $word) {
                if (mb_strlen($word) >= 3) {
                    $p = mb_stripos($text, $word);
                    if ($p !== false) { $pos = $p; break; }
                }
            }
        }

        $start = max(0, ($pos ?? 0) - 60);
        if ($start > 0) {
            $spacePos = mb_strpos($text, ' ', $start);
            $start    = ($spacePos !== false) ? $spacePos + 1 : $start;
        }

        $snippet = mb_substr($text, $start, $maxLen);
        $textLen = mb_strlen($text);

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
