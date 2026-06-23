<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ExtractDocumentContent;
use App\Models\Resource;
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
}
