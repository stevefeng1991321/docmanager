<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BasicKnowledgeTrend;
use App\Models\Category;
use Illuminate\Http\Request;

class BasicKnowledgeController extends Controller
{
    public function index(Request $request)
    {
        $q           = trim($request->get('q', ''));
        $category_id = $request->get('category_id', '');
        $sort        = $request->get('sort', 'newest');

        $query = BasicKnowledgeTrend::where('status', 'published');

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

        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        match ($sort) {
            'title_asc'  => $query->orderBy('title'),
            'title_desc' => $query->orderByDesc('title'),
            'oldest'     => $query->orderBy('created_at'),
            default      => $query->orderByDesc('created_at'),
        };

        $trends     = $query->with('category')->paginate(20)->withQueryString();
        $usedIds    = BasicKnowledgeTrend::where('status', 'published')->distinct()->pluck('category_id');
        $categories = Category::whereIn('id', $usedIds)->orderBy('name')->get();

        return view('basic-knowledge.index', compact('trends', 'q', 'category_id', 'sort', 'categories'));
    }

    public function show(BasicKnowledgeTrend $trend)
    {
        abort_unless($trend->status === 'published', 404);

        $trend->load('media');

        $related = BasicKnowledgeTrend::where('status', 'published')
            ->where('category_id', $trend->category_id)
            ->where('id', '!=', $trend->id)
            ->latest()
            ->limit(4)
            ->get();

        return view('basic-knowledge.show', compact('trend', 'related'));
    }
}
