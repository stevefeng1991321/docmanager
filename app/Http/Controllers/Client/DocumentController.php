<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DownloadLog;
use App\Models\RecentlyViewed;
use App\Models\Resource;
use App\Models\Share;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function show(Resource $resource)
    {
        abort_if(!$resource->isPublished(), 404);

        if (auth()->id()) {
            RecentlyViewed::record(auth()->id(), Resource::class, $resource->id);
        }

        $resource->load(['category', 'tags', 'versions', 'ratings']);
        $userRating  = $resource->ratings()->where('user_id', auth()->id())->first();
        $isFavorited = auth()->user()->favorites()->where('resource_id', $resource->id)->exists();

        $related = Resource::published()
            ->where('category_id', $resource->category_id)
            ->where('id', '!=', $resource->id)
            ->withAvg('ratings', 'rating')
            ->orderByDesc('download_count')
            ->take(5)
            ->get();

        AuditLog::record('document.viewed', $resource->id, []);

        return view('documents.show', compact('resource', 'userRating', 'isFavorited', 'related'));
    }

    public function preview(Resource $resource)
    {
        abort_if(!$resource->isPublished(), 404);
        abort_if($resource->isLocked(), 403, 'This document is currently locked and cannot be previewed.');

        // Only PDF and image files can be previewed (file_type stored as MIME type)
        $ft = strtolower($resource->file_type ?? '');
        abort_if($ft !== 'application/pdf' && !str_starts_with($ft, 'image/'), 404);

        return view('documents.preview', compact('resource'));
    }

    public function download(Resource $resource)
    {
        abort_if(!$resource->isPublished(), 404);
        if ($resource->isLocked()) {
            return back()->with('message', 'This document is currently locked and cannot be downloaded.')->with('status', 'error');
        }
        abort_if(!Storage::exists($resource->file_path), 404);

        // Log download
        DownloadLog::create([
            'user_id'     => auth()->id(),
            'resource_id' => $resource->id,
            'ip_address'  => request()->ip(),
        ]);
        $resource->increment('download_count');

        AuditLog::record('document.downloaded', $resource->id, [
            'user_id' => auth()->id(),
        ]);

        return Storage::download($resource->file_path, $resource->original_filename);
    }

    public function stream(Resource $resource)
    {
        abort_if(!$resource->isPublished(), 404);
        abort_if($resource->isLocked(), 403);
        $ft = strtolower($resource->file_type ?? '');
        abort_if($ft !== 'application/pdf' && !str_starts_with($ft, 'image/'), 404);
        abort_if(!Storage::exists($resource->file_path), 404);

        return response()->file(
            Storage::disk('local')->path($resource->file_path),
            ['Content-Type' => $resource->file_type]
        );
    }

    public function share(Request $request, Resource $resource)
    {
        abort_if(!$resource->isPublished(), 404);

        $expiresAt = now()->addHours(config('app.share_link_expiry_hours', 24));

        $share = Share::create([
            'resource_id' => $resource->id,
            'created_by'  => auth()->id(),
            'token'       => Str::random(40),
            'expires_at'  => $expiresAt,
        ]);

        return back()->with('share_url', route('share.show', $share->token));
    }
}
