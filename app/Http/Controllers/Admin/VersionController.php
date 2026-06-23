<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DocumentVersion;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VersionController extends Controller
{
    public function store(Request $request, Resource $resource)
    {
        $request->validate([
            'file'        => ['required', 'file', 'max:' . (config('app.max_upload_size_mb', 50) * 1024)],
            'change_note' => ['nullable', 'string', 'max:500'],
        ]);

        $file         = $request->file('file');
        $storedName   = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path         = $file->storeAs('resources', $storedName, 'local');
        $hash         = hash_file('sha256', $file->getRealPath());
        $nextVersion  = ($resource->versions()->max('version_number') ?? 0) + 1;

        DocumentVersion::create([
            'resource_id'    => $resource->id,
            'version_number' => $nextVersion,
            'file_path'      => $path,
            'stored_filename'=> $storedName,
            'file_size'      => $file->getSize(),
            'file_hash'      => $hash,
            'change_note'    => $request->change_note,
            'uploaded_by'    => auth()->id(),
            'created_at'     => now(),
        ]);

        // Update resource to point at new version
        $resource->update([
            'stored_filename'   => $storedName,
            'file_path'         => $path,
            'file_size'         => $file->getSize(),
            'file_hash'         => $hash,
            'original_filename' => $file->getClientOriginalName(),
            'file_type'         => $file->getMimeType(),
        ]);

        AuditLog::record('document.version_uploaded', $resource->id, ['version' => $nextVersion]);

        return back()->with('message', "Version {$nextVersion} uploaded.");
    }

    public function restore(Resource $resource, DocumentVersion $version)
    {
        $resource->update([
            'stored_filename'   => $version->stored_filename,
            'file_path'         => $version->file_path,
            'file_size'         => $version->file_size,
            'file_hash'         => $version->file_hash,
        ]);

        AuditLog::record('document.version_restored', $resource->id, ['version' => $version->version_number]);

        return back()->with('message', "Restored to version {$version->version_number}.");
    }
}
