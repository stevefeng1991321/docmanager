<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'date_desc');
        $view = $request->input('view', session('doc_view', 'grid'));
        session(['doc_view' => $view]);

        $categories = Cache::remember('home.categories', 3600, function () {
            return Category::withCount('resources')
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->get();
        });

        $featured = Cache::remember('home.featured', 900, function () {
            return Resource::published()
                ->withAvg('ratings', 'rating')
                ->with(['category'])
                ->orderByDesc('download_count')
                ->take(6)
                ->get();
        });

        $allDocs = Resource::published()
            ->withAvg('ratings', 'rating')
            ->with(['category']);

        $allDocs = match($sort) {
            'name_asc'   => $allDocs->orderBy('title'),
            'name_desc'  => $allDocs->orderByDesc('title'),
            'size_desc'  => $allDocs->orderByDesc('file_size'),
            'downloads'  => $allDocs->orderByDesc('download_count'),
            'date_asc'   => $allDocs->orderBy('created_at'),
            default      => $allDocs->orderByDesc('created_at'),
        };

        $allDocs = $allDocs->paginate(18)->withQueryString();

        return view('home.index', compact('categories', 'featured', 'allDocs', 'sort', 'view'));
    }
}
