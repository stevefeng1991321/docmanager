<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount('resources')->orderBy('name')->paginate(30);
        return view('admin.tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:100', 'unique:tags']]);
        Tag::create(['name' => $request->name, 'slug' => Str::slug($request->name)]);
        return back()->with('message', "Tag \"{$request->name}\" created.");
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate(['name' => ['required', 'string', 'max:100', 'unique:tags,name,' . $tag->id]]);
        $tag->update(['name' => $request->name, 'slug' => Str::slug($request->name)]);
        return back()->with('message', 'Tag updated.');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return back()->with('message', 'Tag deleted.');
    }

    public function merge(Request $request)
    {
        $request->validate([
            'source_id' => ['required', 'exists:tags,id'],
            'target_id' => ['required', 'exists:tags,id', 'different:source_id'],
        ]);

        $source = Tag::findOrFail($request->source_id);
        $target = Tag::findOrFail($request->target_id);

        // Move all resource_tags from source → target (avoid duplicates)
        $sourceResourceIds = \DB::table('resource_tags')->where('tag_id', $source->id)->pluck('resource_id');
        $existingIds = \DB::table('resource_tags')->where('tag_id', $target->id)->pluck('resource_id');

        $toInsert = $sourceResourceIds->diff($existingIds)->map(fn($rid) => ['resource_id' => $rid, 'tag_id' => $target->id]);
        if ($toInsert->isNotEmpty()) {
            \DB::table('resource_tags')->insert($toInsert->values()->all());
        }

        $source->delete();

        return back()->with('message', "Tag \"{$source->name}\" merged into \"{$target->name}\".");
    }
}
