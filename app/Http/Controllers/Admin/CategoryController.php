<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['resources', 'children'])
            ->with(['children' => fn($q) => $q->withCount('resources')->orderBy('sort_order')])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
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
        Cache::forget('home.categories.tree');

        return back()->with('message', "Category \"{$category->name}\" created.");
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        $parentId = $request->parent_id ?: null;

        if ($parentId && $parentId == $category->id) {
            return back()->with('message', 'A category cannot be its own parent.')->with('status', 'error');
        }

        if ($parentId && $category->children()->where('id', $parentId)->exists()) {
            return back()->with('message', 'A category cannot have one of its subcategories as parent.')->with('status', 'error');
        }

        $category->update([
            'name'      => $request->name,
            'slug'      => Str::slug($request->name),
            'parent_id' => $parentId,
        ]);

        AuditLog::record('category.updated', null, ['name' => $category->name]);
        Cache::forget('home.categories.tree');

        return back()->with('message', "Category \"{$category->name}\" updated.");
    }

    public function destroy(Category $category)
    {
        $docCount = $category->resources()->count();
        if ($docCount > 0) {
            return back()->with('message', "Cannot delete \"{$category->name}\": {$docCount} document(s) are assigned to it. Reassign them first.")->with('status', 'error');
        }

        if ($category->children()->exists()) {
            return back()->with('message', "Cannot delete \"{$category->name}\": it has subcategories. Delete them first.")->with('status', 'error');
        }

        $name = $category->name;
        $category->delete();
        AuditLog::record('category.deleted', null, ['name' => $name]);
        Cache::forget('home.categories.tree');

        return back()->with('message', "Category \"{$name}\" deleted.");
    }
}
