<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('children')->whereNull('parent_id')->orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        $category = Category::create([
            'name'      => $request->name,
            'slug'      => Str::slug($request->name),
            'parent_id' => $request->parent_id,
        ]);

        AuditLog::record('category.created', null, ['name' => $category->name]);

        return back()->with('message', "Category \"{$category->name}\" created.");
    }

    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => ['required', 'string', 'max:100']]);
        $category->update(['name' => $request->name, 'slug' => Str::slug($request->name)]);
        return back()->with('message', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('message', 'Category deleted.');
    }
}
