<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Tag;

class TagController extends Controller
{
    public function show(Tag $tag)
    {
        $resources = $tag->resources()
            ->published()
            ->with(['category'])
            ->latest()
            ->paginate(15);

        return view('tags.show', compact('tag', 'resources'));
    }
}
