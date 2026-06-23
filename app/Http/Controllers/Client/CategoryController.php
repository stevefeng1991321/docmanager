<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $resources = $category->resources()
            ->published()
            ->with(['tags'])
            ->latest()
            ->paginate(15);

        $subcategories = $category->children()->withCount('resources')->get();

        return view('categories.show', compact('category', 'resources', 'subcategories'));
    }
}
