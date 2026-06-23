<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Resource;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = auth()->user()
            ->favorites()
            ->with(['resource.category', 'resource.tags'])
            ->latest()
            ->paginate(20);

        return view('favorites.index', compact('favorites'));
    }

    public function store(Resource $resource)
    {
        Favorite::firstOrCreate([
            'user_id'     => auth()->id(),
            'resource_id' => $resource->id,
        ]);

        return back()->with('message', 'Added to favorites.');
    }

    public function destroy(Resource $resource)
    {
        Favorite::where('user_id', auth()->id())
            ->where('resource_id', $resource->id)
            ->delete();

        return back()->with('message', 'Removed from favorites.');
    }
}
