<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $prefs   = Auth::user()->preferences;
        $sort    = $request->input('sort', 'date_desc');
        $view    = $request->input('view', $prefs?->view_mode ?? 'grid');
        $perPage = in_array((int) $request->input('per_page'), config('pagination.per_page_options'))
            ? (int) $request->input('per_page')
            : ($prefs?->items_per_page ?? config('pagination.default_per_page'));
        session(['doc_view' => $view]);

        $categories = Cache::remember('home.categories.tree', 3600, function () {
            return Category::with(['children' => fn ($q) => $q->withCount(['resources' => fn ($q) => $q->where('status', 'published')])->orderBy('sort_order')])
                ->withCount(['resources' => fn ($q) => $q->where('status', 'published')])
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->get();
        });

        $allDocs = Resource::published()
            ->withAvg('ratings', 'rating')
            ->with(['category']);

        $allDocs = $allDocs->sorted($sort);

        $allDocs = $allDocs->paginate($perPage)->withQueryString();

        return view('home.index', compact('categories', 'allDocs', 'sort', 'view', 'perPage'));
    }

    public function browse(Request $request)
    {
        $prefs        = Auth::user()->preferences;
        $sort         = $request->input('sort', 'date_desc');
        $view         = $request->input('view', session('doc_view', $prefs?->view_mode ?? 'grid'));
        $perPage      = in_array((int) $request->input('per_page'), config('pagination.per_page_options'))
            ? (int) $request->input('per_page')
            : ($prefs?->items_per_page ?? config('pagination.default_per_page'));
        $categorySlug = $request->input('category');

        $category = $categorySlug
            ? Category::with('children')->where('slug', $categorySlug)->first()
            : null;

        $categoryIds = [];
        if ($category) {
            $categoryIds = [$category->id, ...$category->children->pluck('id')->toArray()];
        }

        $allDocs = Resource::published()
            ->withAvg('ratings', 'rating')
            ->with(['category'])
            ->when($categoryIds, fn ($q) => $q->whereIn('category_id', $categoryIds));

        $allDocs = $allDocs->sorted($sort);

        $allDocs = $allDocs->paginate($perPage)->withQueryString();

        return view('home._docs', compact('allDocs', 'sort', 'view', 'category', 'perPage'));
    }
}
