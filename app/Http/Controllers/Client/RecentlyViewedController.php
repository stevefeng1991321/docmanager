<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BasicKnowledgeTrend;
use App\Models\RecentlyViewed;
use App\Models\Resource;

class RecentlyViewedController extends Controller
{
    public function index()
    {
        $history = RecentlyViewed::with([
                'viewable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Resource::class           => ['category'],
                        BasicKnowledgeTrend::class => ['category'],
                    ]);
                },
            ])
            ->where('user_id', auth()->id())
            ->orderByDesc('viewed_at')
            ->limit(10)
            ->get();

        return view('history.index', compact('history'));
    }
}
