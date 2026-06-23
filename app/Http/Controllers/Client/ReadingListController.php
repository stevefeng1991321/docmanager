<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ReadingList;
use App\Models\ReadingListItem;
use App\Models\Resource;
use Illuminate\Http\Request;

class ReadingListController extends Controller
{
    public function index()
    {
        $lists = auth()->user()
            ->readingLists()
            ->withCount('items')
            ->latest()
            ->get();

        return view('reading-lists.index', compact('lists'));
    }

    public function show(ReadingList $readingList)
    {
        abort_if($readingList->user_id !== auth()->id(), 403);

        $readingList->load(['items.resource.category', 'items.resource.tags']);

        return view('reading-lists.show', compact('readingList'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:255']]);

        $list = ReadingList::create([
            'user_id' => auth()->id(),
            'name'    => $request->name,
        ]);

        return back()->with('message', "Reading list \"{$list->name}\" created.");
    }

    public function update(Request $request, ReadingList $readingList)
    {
        abort_if($readingList->user_id !== auth()->id(), 403);
        $request->validate(['name' => ['required', 'string', 'max:255']]);
        $readingList->update(['name' => $request->name]);
        return back()->with('message', 'Reading list updated.');
    }

    public function destroy(ReadingList $readingList)
    {
        abort_if($readingList->user_id !== auth()->id(), 403);
        $readingList->delete();
        return redirect()->route('reading-lists.index')->with('message', 'Reading list deleted.');
    }

    public function addItem(ReadingList $readingList, Resource $resource)
    {
        abort_if($readingList->user_id !== auth()->id(), 403);

        $maxOrder = $readingList->items()->max('sort_order') ?? 0;

        ReadingListItem::firstOrCreate(
            ['reading_list_id' => $readingList->id, 'resource_id' => $resource->id],
            ['sort_order' => $maxOrder + 1]
        );

        return back()->with('message', 'Document added to list.');
    }

    public function removeItem(ReadingList $readingList, Resource $resource)
    {
        abort_if($readingList->user_id !== auth()->id(), 403);

        ReadingListItem::where('reading_list_id', $readingList->id)
            ->where('resource_id', $resource->id)
            ->delete();

        return back()->with('message', 'Document removed from list.');
    }
}
