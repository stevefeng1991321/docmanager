<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BasicKnowledgeTrend;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BasicKnowledgeTrendController extends Controller
{
    public function index(Request $request)
    {
        $q           = trim($request->get('q', ''));
        $status      = $request->get('status', '');
        $category_id = $request->get('category_id', '');
        $sort        = $request->get('sort', 'newest');

        $query = BasicKnowledgeTrend::query();

        if ($q) {
            if (mb_strlen($q) >= 3) {
                $query->whereFullText(['title', 'summary', 'content'], $q);
            } else {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title',   'like', "%{$q}%")
                        ->orWhere('summary', 'like', "%{$q}%")
                        ->orWhere('content', 'like', "%{$q}%");
                });
            }
        }

        if ($status)      { $query->where('status', $status); }
        if ($category_id) { $query->where('category_id', $category_id); }

        match ($sort) {
            'title_asc'  => $query->orderBy('title'),
            'title_desc' => $query->orderByDesc('title'),
            'oldest'     => $query->orderBy('created_at')->orderBy('id'),
            default      => $query->orderByDesc('created_at'),
        };

        $trends     = $query->with('category')->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.basic-knowledge.index', compact('trends', 'q', 'status', 'category_id', 'sort', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.basic-knowledge.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'content'     => ['required', 'string'],
            'summary'     => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'tags'        => ['nullable', 'string'],
            'status'      => ['required', 'in:draft,published,archived'],
        ]);

        $validated['tags'] = $this->parseTags($request->input('tags'));

        $trend = BasicKnowledgeTrend::create($validated);

        return redirect()->route('admin.basic-knowledge.edit', $trend)
            ->with('message', 'Entry created. You can now add media below.');
    }

    public function show(BasicKnowledgeTrend $trend)
    {
        $trend->load('media');

        $related = BasicKnowledgeTrend::where('id', '!=', $trend->id)
            ->where(function ($q) use ($trend) {
                $q->where('category_id', $trend->category_id);
                foreach ((array) ($trend->tags ?? []) as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            })
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.basic-knowledge.show', compact('trend', 'related'));
    }

    public function edit(BasicKnowledgeTrend $trend)
    {
        $trend->load('media');
        $categories = Category::orderBy('name')->get();

        return view('admin.basic-knowledge.edit', compact('trend', 'categories'));
    }

    public function update(Request $request, BasicKnowledgeTrend $trend)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'content'     => ['required', 'string'],
            'summary'     => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'tags'        => ['nullable', 'string'],
            'status'      => ['required', 'in:draft,published,archived'],
        ]);

        $validated['tags'] = $this->parseTags($request->input('tags'));

        $trend->update($validated);

        return redirect()->route('admin.basic-knowledge.index')
            ->with('message', 'Entry updated successfully.');
    }

    public function bulkAction(Request $request)
    {
        $ids    = array_filter(array_map('intval', (array) $request->input('ids', [])));
        $action = $request->input('action');

        if (empty($ids) || !in_array($action, ['publish', 'archive', 'delete'])) {
            return back()->with('error', 'Select at least one entry and a valid action.');
        }

        $query = BasicKnowledgeTrend::whereIn('id', $ids);
        $count = $query->count();

        match ($action) {
            'publish' => $query->update(['status' => 'published']),
            'archive' => $query->update(['status' => 'archived']),
            'delete'  => $query->delete(),
        };

        $label = match ($action) {
            'publish' => 'published',
            'archive' => 'archived',
            'delete'  => 'deleted',
        };

        return back()->with('message', "{$count} " . Str::plural('entry', $count) . " {$label}.");
    }

    public function destroy(BasicKnowledgeTrend $trend)
    {
        $trend->delete();

        return redirect()->route('admin.basic-knowledge.index')
            ->with('message', 'Entry deleted.');
    }

    private function parseTags(?string $raw): ?array
    {
        if (!$raw || !trim($raw)) {
            return null;
        }

        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }
}
