<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Resource;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'resource_id' => ['required', 'exists:resources,id'],
            'page_number' => ['required', 'integer', 'min:1'],
            'label'        => ['nullable', 'string', 'max:100'],
        ]);

        $resource = Resource::findOrFail($request->resource_id);
        abort_if(!$resource->isPublished(), 403);

        $bookmark = Bookmark::create([
            'user_id'     => auth()->id(),
            'resource_id' => $request->resource_id,
            'page_number' => $request->page_number,
            'label'        => $request->label,
        ]);

        return response()->json(['id' => $bookmark->id, 'message' => 'Bookmark saved.']);
    }

    public function update(Request $request, Bookmark $bookmark)
    {
        abort_if($bookmark->user_id !== auth()->id(), 403);
        $request->validate(['label' => ['nullable', 'string', 'max:100']]);
        $bookmark->update(['label' => $request->label]);
        return response()->json(['message' => 'Bookmark updated.']);
    }

    public function destroy(Bookmark $bookmark)
    {
        abort_if($bookmark->user_id !== auth()->id(), 403);
        $bookmark->delete();
        return response()->json(['message' => 'Bookmark removed.']);
    }
}
