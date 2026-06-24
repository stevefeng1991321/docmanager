<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreResourceRequest;
use App\Http\Requests\Api\UpdateResourceRequest;
use App\Http\Resources\DocumentResource;
use App\Jobs\ExtractDocumentContent;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\DocumentVersion;
use App\Models\DownloadLog;
use App\Models\Resource;
use App\Models\Share;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $query      = trim($request->input('q', ''));
        $type       = $request->input('type');
        $categoryId = $request->input('category_id');
        $status     = $request->input('status', 'published');
        $sort       = $request->input('sort', 'date_desc');
        $perPage    = min((int) $request->input('per_page', 15), 100);

        $builder = Resource::with(['category', 'tags', 'uploader'])
            ->withAvg('ratings', 'rating')
            ->when($status,     fn($q, $s) => $q->where('status', $s))
            ->when($type,       fn($q, $t) => $q->where('file_type', 'like', "%{$t}%"))
            ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
            ->when($query !== '', function ($q) use ($query) {
                $escaped = addslashes($query);
                if (strlen($query) >= 3) {
                    $q->whereRaw(
                        "MATCH(title, description, content) AGAINST (? IN BOOLEAN MODE)",
                        ["+{$escaped}*"]
                    );
                } else {
                    $q->where(fn($i) => $i->where('title', 'like', "%{$query}%")
                                          ->orWhere('description', 'like', "%{$query}%"));
                }
            });

        $builder = $builder->sorted($sort);

        $resources = $builder->paginate($perPage);

        return DocumentResource::collection($resources);
    }

    public function show(Resource $resource)
    {
        abort_if($resource->status !== 'published' && !$this->userCanManage(), 404);

        $resource->load(['category', 'tags', 'uploader', 'versions']);

        return new DocumentResource($resource);
    }

    public function store(StoreResourceRequest $request)
    {
        $this->requireEditorOrAdmin();

        $file        = $request->file('file');

        if ($request->user()->wouldExceedQuota($file->getSize())) {
            return response()->json(['message' => 'Upload would exceed your storage quota.'], 422);
        }

        $storedName  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path        = $file->storeAs('resources', $storedName, 'local');
        $hash        = hash_file('sha256', $file->getRealPath());

        $resource = Resource::create([
            'title'             => $request->title,
            'description'       => $request->description,
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename'   => $storedName,
            'file_path'         => $path,
            'file_type'         => $file->getMimeType(),
            'file_size'         => $file->getSize(),
            'file_hash'         => $hash,
            'category_id'       => $request->category_id,
            'uploaded_by'       => $request->user()->id,
            'status'            => $request->input('status', 'draft'),
        ]);

        if ($request->tags) {
            $resource->tags()->sync($request->tags);
        }

        DocumentVersion::create([
            'resource_id'     => $resource->id,
            'version_number'  => 1,
            'file_path'       => $path,
            'stored_filename' => $storedName,
            'file_size'       => $file->getSize(),
            'file_hash'       => $hash,
            'change_note'     => 'Initial upload via API',
            'uploaded_by'     => $request->user()->id,
            'created_at'      => now(),
        ]);

        AuditLog::record('document.uploaded', $resource->id, ['source' => 'api']);
        ExtractDocumentContent::dispatch($resource);
        $this->clearDocumentCaches();

        return (new DocumentResource($resource->load('category', 'tags')))
            ->response()->setStatusCode(201);
    }

    public function update(UpdateResourceRequest $request, Resource $resource)
    {
        $this->requireEditorOrAdmin();

        $resource->update($request->only('title', 'description', 'category_id', 'status'));

        if ($request->has('tags')) {
            $resource->tags()->sync($request->tags ?? []);
        }

        AuditLog::record('document.updated', $resource->id, ['source' => 'api']);
        $this->clearDocumentCaches();

        return new DocumentResource($resource->fresh(['category', 'tags']));
    }

    public function destroy(Resource $resource)
    {
        $this->requireEditorOrAdmin();

        $resource->delete();
        AuditLog::record('document.deleted', $resource->id, ['source' => 'api']);
        $this->clearDocumentCaches();

        return response()->json(['message' => 'Document deleted.']);
    }

    public function download(Request $request, Resource $resource)
    {
        abort_if($resource->status !== 'published', 404);
        abort_if(!Storage::exists($resource->file_path), 404);

        DownloadLog::create([
            'user_id'     => $request->user()->id,
            'resource_id' => $resource->id,
            'ip_address'  => $request->ip(),
        ]);
        $resource->increment('download_count');

        return Storage::download($resource->file_path, $resource->original_filename);
    }

    public function share(Request $request, Resource $resource)
    {
        abort_if($resource->status !== 'published', 404);

        $share = Share::create([
            'resource_id' => $resource->id,
            'created_by'  => $request->user()->id,
            'token'       => Str::random(40),
            'expires_at'  => now()->addHours(config('app.share_link_expiry_hours', 24)),
        ]);

        return response()->json([
            'share_url'  => route('share.show', $share->token),
            'expires_at' => $share->expires_at->toIso8601String(),
        ]);
    }

    // ---------- helpers ----------

    private function userCanManage(): bool
    {
        $user = auth('sanctum')->user();
        return $user && in_array($user->role, ['admin', 'editor']);
    }

    private function requireEditorOrAdmin(): void
    {
        $user = request()->user();
        abort_if(!$user || !in_array($user->role, ['admin', 'editor']), 403, 'Insufficient permissions.');
    }
}
