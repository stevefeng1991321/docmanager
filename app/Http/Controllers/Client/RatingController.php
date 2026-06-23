<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DocumentRating;
use App\Models\Resource;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request, Resource $resource)
    {
        abort_if(!$resource->isPublished(), 404);

        $request->validate([
            'score'  => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        DocumentRating::updateOrCreate(
            ['user_id' => auth()->id(), 'resource_id' => $resource->id],
            ['rating' => $request->score, 'review' => $request->review]
        );

        return back()->with('message', 'Rating submitted.');
    }
}
