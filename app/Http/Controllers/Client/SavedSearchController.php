<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\SavedSearch;
use Illuminate\Http\Request;

class SavedSearchController extends Controller
{
    public function index()
    {
        $searches = auth()->user()->savedSearches()->latest()->get();
        return view('saved-searches.index', compact('searches'));
    }

    public function store(Request $request)
    {
        $request->validate(['query' => ['required', 'string', 'max:255']]);

        $q = $request->input('query');

        SavedSearch::firstOrCreate(
            ['user_id' => auth()->id(), 'query' => $q],
            ['name' => $q]
        );

        return back()->with('message', 'Search saved.');
    }

    public function destroy(SavedSearch $savedSearch)
    {
        abort_if($savedSearch->user_id !== auth()->id(), 403);
        $savedSearch->delete();
        return back()->with('message', 'Saved search removed.');
    }
}
