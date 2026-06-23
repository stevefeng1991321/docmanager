<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Share;
use Illuminate\Support\Facades\Storage;

class ShareController extends Controller
{
    public function show(string $token)
    {
        $share = Share::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $resource = $share->resource;
        abort_if(!$resource || !$resource->isPublished(), 404);

        return view('share.show', compact('share', 'resource'));
    }
}
