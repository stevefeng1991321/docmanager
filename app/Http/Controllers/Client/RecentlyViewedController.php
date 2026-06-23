<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\RecentlyViewed;

class RecentlyViewedController extends Controller
{
    public function index()
    {
        $history = RecentlyViewed::with(['resource.category'])
            ->where('user_id', auth()->id())
            ->orderByDesc('viewed_at')
            ->paginate(20);

        return view('history.index', compact('history'));
    }
}
