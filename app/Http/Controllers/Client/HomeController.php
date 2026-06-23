<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Resource;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('resources')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $featured = Resource::published()
            ->with(['category', 'tags'])
            ->orderByDesc('download_count')
            ->take(6)
            ->get();

        $recent = Resource::published()
            ->with(['category'])
            ->latest()
            ->take(10)
            ->get();

        return view('home.index', compact('categories', 'featured', 'recent'));
    }
}
