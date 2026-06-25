<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ExtractDocumentContent;
use App\Models\Resource;
use App\Models\ResourceEmbedding;
use App\Models\SearchLog;
use App\Services\TfidfService;
use Illuminate\Support\Facades\Artisan;
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

        $zeroResultQueries = SearchLog::selectRaw('query, COUNT(*) as count')
            ->where('results_count', 0)
            ->groupBy('query')
            ->orderByDesc('count')
            ->take(20)
            ->get();

        $tfidf         = app(TfidfService::class);
        $tfidfReady    = $tfidf->hasIndex();
        $tfidfIndexed  = ResourceEmbedding::where('model', 'tfidf-v1')->count();

        return view('admin.search.index', compact('topQueries', 'zeroResultQueries', 'tfidfReady', 'tfidfIndexed'));
    }

    public function reindex()
    {
        $count = 0;

        Resource::whereNull('deleted_at')
            ->select(['id', 'file_path', 'original_filename'])
            ->chunk(50, function ($resources) use (&$count) {
                foreach ($resources as $resource) {
                    ExtractDocumentContent::dispatch($resource);
                    $count++;
                }
            });

        return back()->with('message', "Re-index queued for {$count} document(s). Run the queue worker to process.");
    }

    public function buildTfidf()
    {
        Artisan::call('search:build-tfidf');
        $indexed = ResourceEmbedding::where('model', 'tfidf-v1')->count();
        return back()->with('message', "TF-IDF index built. {$indexed} document(s) indexed.");
    }
}
