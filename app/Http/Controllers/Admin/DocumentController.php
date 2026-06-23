<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\DocumentVersion;
use App\Models\Notification;
use App\Models\Resource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $documents = Resource::with('uploader', 'category')
            ->when($request->search,   fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->status,   fn($q, $s) => $q->where('status', $s))
            ->when($request->category, fn($q, $c) => $q->where('category_id', $c))
            ->when($request->type,     fn($q, $t) => $q->where('file_type', 'like', "%{$t}%"))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.documents.index', compact('documents', 'categories'));
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
        $originalName    = $file->getClientOriginalName();
        $storedName      = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path            = $file->storeAs('resources', $storedName, 'local');
        $hash            = hash_file('sha256', $file->getRealPath());

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

        return redirect()->route('admin.documents.index')
            ->with('message', "Document \"{$resource->title}\" uploaded.");
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

        return redirect()->route('admin.documents.edit', $document)
            ->with('message', 'Document updated.');
    }

    public function destroy(Resource $document)
    {
        $document->delete(); // soft delete
        AuditLog::record('document.deleted', $document->id, ['title' => $document->title]);

        return redirect()->route('admin.documents.index')
            ->with('message', "Document moved to Trash.");
    }

    public function trash()
    {
        $documents = Resource::onlyTrashed()->with('uploader')->latest('deleted_at')->paginate(20);
        return view('admin.documents.trash', compact('documents'));
    }

    public function restore(Resource $document)
    {
        $document->restore();
        AuditLog::record('document.restored', $document->id);
        return back()->with('message', 'Document restored.');
    }

    public function forceDelete(Resource $document)
    {
        Storage::disk('local')->delete($document->file_path);
        $document->forceDelete();
        AuditLog::record('document.permanently_deleted', null, ['title' => $document->title]);
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

    public function approve(Request $request, Resource $document)
    {
        $document->update(['status' => 'published']);
        AuditLog::record('document.approved', $document->id);

        // Notify uploader
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

        if ($document->uploaded_by) {
            Notification::send($document->uploaded_by, 'doc_rejected',
                'Document Rejected', $request->reason ?? "\"{$document->title}\" was not approved.", $document->id);
        }

        return back()->with('message', 'Document rejected.');
    }
}
