<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ExtractDocumentContent;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\DocumentAccessLog;
use App\Models\DocumentVersion;
use App\Models\Notification;
use App\Models\Resource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'date_desc');

        $query = Resource::with('uploader', 'category')
            ->when($request->search,   fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->status,   fn($q, $s) => $q->where('status', $s))
            ->when($request->category, fn($q, $c) => $q->where('category_id', $c))
            ->when($request->type,     fn($q, $t) => $q->where('file_type', 'like', "%{$t}%"));

        $query = match($sort) {
            'name_asc'  => $query->orderBy('title'),
            'name_desc' => $query->orderByDesc('title'),
            'size_desc' => $query->orderByDesc('file_size'),
            'downloads' => $query->orderByDesc('download_count'),
            'date_asc'  => $query->orderBy('created_at'),
            default     => $query->orderByDesc('created_at'),
        };

        $documents  = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.documents.index', compact('documents', 'categories', 'sort'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags       = Tag::orderBy('name')->get();
        return view('admin.documents.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file'        => ['required', 'file', 'max:' . (config('app.max_upload_size_mb', 50) * 1024)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['exists:tags,id'],
            'status'      => ['required', 'in:draft,pending_review,published'],
        ]);

        $file            = $request->file('file');

        if (auth()->user()->wouldExceedQuota($file->getSize())) {
            return back()->withErrors(['file' => 'This upload would exceed your storage quota.'])->withInput();
        }

        $hash         = hash_file('sha256', $file->getRealPath());
        $duplicate    = Resource::where('file_hash', $hash)->whereNull('deleted_at')->first();
        if ($duplicate) {
            return back()->withErrors(['file' => "A document with identical content already exists: \"{$duplicate->title}\" (ID {$duplicate->id})."])->withInput();
        }

        $originalName    = $file->getClientOriginalName();
        $storedName      = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path            = $file->storeAs('resources', $storedName, 'local');

        $resource = Resource::create([
            'title'             => $request->title,
            'description'       => $request->description,
            'original_filename' => $originalName,
            'stored_filename'   => $storedName,
            'file_path'         => $path,
            'file_type'         => $file->getMimeType(),
            'file_size'         => $file->getSize(),
            'file_hash'         => $hash,
            'category_id'       => $request->category_id,
            'uploaded_by'       => auth()->id(),
            'status'            => $request->status,
        ]);

        if ($request->tags) {
            $resource->tags()->sync($request->tags);
        }

        // First version entry
        DocumentVersion::create([
            'resource_id'    => $resource->id,
            'version_number' => 1,
            'file_path'      => $path,
            'stored_filename'=> $storedName,
            'file_size'      => $file->getSize(),
            'file_hash'      => $hash,
            'change_note'    => 'Initial upload',
            'uploaded_by'    => auth()->id(),
            'created_at'     => now(),
        ]);

        AuditLog::record('document.uploaded', $resource->id, ['title' => $resource->title]);

        ExtractDocumentContent::dispatch($resource);
        self::clearDocumentCaches();

        return redirect()->route('admin.documents.index')
            ->with('message', "Document \"{$resource->title}\" uploaded. Content indexing queued.");
    }

    public function edit(Resource $document)
    {
        $document->load('tags', 'category', 'versions.uploader');
        $categories = Category::orderBy('name')->get();
        $tags       = Tag::orderBy('name')->get();
        return view('admin.documents.edit', compact('document', 'categories', 'tags'));
    }

    public function update(Request $request, Resource $document)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['exists:tags,id'],
        ]);

        $document->update($request->only('title', 'description', 'category_id'));
        $document->tags()->sync($request->tags ?? []);

        AuditLog::record('document.updated', $document->id, ['title' => $document->title]);
        self::clearDocumentCaches();

        return redirect()->route('admin.documents.edit', $document)
            ->with('message', 'Document updated.');
    }

    public function destroy(Resource $document)
    {
        $document->delete(); // soft delete
        AuditLog::record('document.deleted', $document->id, ['title' => $document->title]);
        self::clearDocumentCaches();

        return redirect()->route('admin.documents.index')
            ->with('message', "Document moved to Trash.");
    }

    public function trash()
    {
        $documents = Resource::onlyTrashed()->with('uploader')->latest('deleted_at')->paginate(20);
        return view('admin.documents.trash', compact('documents'));
    }

    public function accessLog(Resource $document)
    {
        $logs = DocumentAccessLog::where('resource_id', $document->id)
            ->with('user', 'version')
            ->latest('created_at')
            ->paginate(50);

        return view('admin.documents.access-log', compact('document', 'logs'));
    }

    public function restore(Resource $document)
    {
        $document->restore();
        AuditLog::record('document.restored', $document->id);
        self::clearDocumentCaches();
        return back()->with('message', 'Document restored.');
    }

    public function forceDelete(Resource $document)
    {
        Storage::disk('local')->delete($document->file_path);
        $document->forceDelete();
        AuditLog::record('document.permanently_deleted', null, ['title' => $document->title]);
        self::clearDocumentCaches();
        return back()->with('message', 'Document permanently deleted.');
    }

    public function lock(Resource $document)
    {
        $document->update(['locked_by' => auth()->id(), 'locked_at' => now()]);
        AuditLog::record('document.locked', $document->id);
        return back()->with('message', 'Document locked.');
    }

    public function unlock(Resource $document)
    {
        $document->update(['locked_by' => null, 'locked_at' => null]);
        AuditLog::record('document.unlocked', $document->id);
        return back()->with('message', 'Document unlocked.');
    }

    // ---------- bulk actions ----------

    public function bulkApprove(Request $request)
    {
        $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']]);

        $count = Resource::whereIn('id', $request->ids)
            ->where('status', 'pending_review')
            ->update(['status' => 'published']);

        AuditLog::record('document.bulk_approved', null, ['count' => $count, 'ids' => $request->ids]);
        self::clearDocumentCaches();

        return back()->with('message', "{$count} document(s) approved and published.");
    }

    public function bulkTrash(Request $request)
    {
        $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']]);

        $count = Resource::whereIn('id', $request->ids)->whereNull('deleted_at')->count();
        Resource::whereIn('id', $request->ids)->delete();

        AuditLog::record('document.bulk_trashed', null, ['count' => $count, 'ids' => $request->ids]);
        self::clearDocumentCaches();

        return back()->with('message', "{$count} document(s) moved to Trash.");
    }

    public function bulkReject(Request $request)
    {
        $request->validate([
            'ids'    => ['required', 'array'],
            'ids.*'  => ['integer'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $count = Resource::whereIn('id', $request->ids)->update(['status' => 'rejected']);

        AuditLog::record('document.bulk_rejected', null, [
            'count'  => $count,
            'ids'    => $request->ids,
            'reason' => $request->reason,
        ]);
        self::clearDocumentCaches();

        return back()->with('message', "{$count} document(s) rejected.");
    }

    public function bulkAssignCategory(Request $request)
    {
        $request->validate([
            'ids'         => ['required', 'array'],
            'ids.*'       => ['integer'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $count = Resource::whereIn('id', $request->ids)->update(['category_id' => $request->category_id]);

        AuditLog::record('document.bulk_category_assigned', null, [
            'count'       => $count,
            'category_id' => $request->category_id,
        ]);
        self::clearDocumentCaches();

        return back()->with('message', "{$count} document(s) assigned to category.");
    }

    public function bulkDownload(Request $request)
    {
        $request->validate(['ids' => ['required', 'array', 'max:100'], 'ids.*' => ['integer']]);

        $documents = Resource::whereIn('id', $request->ids)->whereNull('deleted_at')
            ->get(['id', 'title', 'file_path', 'original_filename']);

        if ($documents->isEmpty()) {
            return back()->withErrors(['ids' => 'No documents found for the selected IDs.']);
        }

        $tmpDir  = storage_path('app/temp');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $zipPath = $tmpDir . '/bulk_' . uniqid() . '.zip';
        $zip     = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            return back()->withErrors(['ids' => 'Could not create ZIP archive.']);
        }

        $seen = [];
        foreach ($documents as $doc) {
            $fullPath = storage_path('app/' . $doc->file_path);
            if (!file_exists($fullPath)) {
                continue;
            }
            // Deduplicate filenames inside the ZIP
            $name = $doc->original_filename;
            if (isset($seen[$name])) {
                $seen[$name]++;
                $ext  = pathinfo($name, PATHINFO_EXTENSION);
                $base = pathinfo($name, PATHINFO_FILENAME);
                $name = $ext ? "{$base}_{$seen[$name]}.{$ext}" : "{$base}_{$seen[$name]}";
            } else {
                $seen[$name] = 0;
            }
            $zip->addFile($fullPath, $name);
        }

        $zip->close();

        AuditLog::record('document.bulk_downloaded', null, [
            'count' => $documents->count(),
            'ids'   => $request->ids,
        ]);

        return response()
            ->download($zipPath, 'documents_' . now()->format('Ymd_His') . '.zip')
            ->deleteFileAfterSend(true);
    }

    private static function clearDocumentCaches(): void
    {
        Cache::forget('dashboard.stats');
        Cache::forget('dashboard.upload_trend');
        Cache::forget('dashboard.download_trend');
        Cache::forget('home.categories');
        Cache::forget('home.featured');
    }

    public function approve(Request $request, Resource $document)
    {
        $document->update(['status' => 'published']);
        AuditLog::record('document.approved', $document->id);
        self::clearDocumentCaches();

        if ($document->uploaded_by) {
            Notification::send($document->uploaded_by, 'doc_approved',
                'Document Published', "\"{$document->title}\" has been approved and published.", $document->id);
        }

        return back()->with('message', 'Document published.');
    }

    public function reject(Request $request, Resource $document)
    {
        $request->validate(['reason' => ['nullable', 'string', 'max:500']]);
        $document->update(['status' => 'rejected']);
        AuditLog::record('document.rejected', $document->id, ['reason' => $request->reason]);
        self::clearDocumentCaches();

        if ($document->uploaded_by) {
            Notification::send($document->uploaded_by, 'doc_rejected',
                'Document Rejected', $request->reason ?? "\"{$document->title}\" was not approved.", $document->id);
        }

        return back()->with('message', 'Document rejected.');
    }
}
