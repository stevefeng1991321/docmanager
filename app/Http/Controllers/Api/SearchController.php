<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Models\Resource;
use App\Models\SearchLog;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query      = trim($request->input('q', ''));
        $type       = $request->input('type');
        $categoryId = $request->input('category_id');
        $dateFrom   = $request->input('date_from');
        $dateTo     = $request->input('date_to');
        $sort       = $request->input('sort', 'relevance');
        $perPage    = min((int) $request->input('per_page', 15), 100);

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

        $builder = $sort === 'relevance'
            ? $builder->orderByDesc('download_count')
            : $builder->sorted($sort);

        $results = $builder->paginate($perPage);

        if ($query !== '') {
            SearchLog::create([
                'user_id'       => $request->user()?->id,
                'query'         => $query,
                'results_count' => $results->total(),
            ]);
        }

        return DocumentResource::collection($results)->additional([
            'meta' => [
                'query'        => $query,
                'total'        => $results->total(),
                'filters'      => compact('type', 'categoryId', 'dateFrom', 'dateTo'),
                'sort'         => $sort,
            ],
        ]);
    }
}
