<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ExtractDocumentContent;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\DocumentVersion;
use App\Models\Resource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChunkedUploadController extends Controller
{
    private string $chunkDir;

    public function __construct()
    {
        $this->chunkDir = storage_path('app/chunks');
    }

    /**
     * Receive a single chunk and store it to a temp directory.
     */
    public function chunk(Request $request)
    {
        $request->validate([
            'file_id'      => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]{8,64}$/'],
            'chunk_index'  => ['required', 'integer', 'min:0'],
            'total_chunks' => ['required', 'integer', 'min:1', 'max:200'],
            'chunk'        => ['required', 'file'],
            'file_size'    => ['nullable', 'integer', 'min:0'],
        ]);

        // Reject on first chunk if total file size would exceed quota
        if ((int) $request->chunk_index === 0 && $request->filled('file_size')) {
            if (auth()->user()->wouldExceedQuota((int) $request->file_size)) {
                return response()->json(['error' => 'This upload would exceed your storage quota.'], 422);
            }
        }

        $dir = $this->chunkDir . '/' . $request->file_id;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $request->file('chunk')->move($dir, 'chunk_' . $request->chunk_index);

        $received = count(glob($dir . '/chunk_*'));

        return response()->json([
            'received'      => $received,
            'total_chunks'  => (int) $request->total_chunks,
            'complete'      => $received >= (int) $request->total_chunks,
        ]);
    }

    /**
     * Assemble all chunks into a document and create the resource record.
     */
    public function assemble(Request $request)
    {
        $request->validate([
            'file_id'           => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]{8,64}$/'],
            'total_chunks'      => ['required', 'integer', 'min:1', 'max:200'],
            'original_name'     => ['required', 'string', 'max:255'],
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'category_id'       => ['nullable', 'exists:categories,id'],
            'tags'              => ['nullable', 'array'],
            'tags.*'            => ['exists:tags,id'],
            'status'            => ['required', 'in:draft,pending_review,published'],
        ]);

        $dir         = $this->chunkDir . '/' . $request->file_id;
        $totalChunks = (int) $request->total_chunks;

        // Verify all chunks are present
        for ($i = 0; $i < $totalChunks; $i++) {
            if (!file_exists($dir . '/chunk_' . $i)) {
                return response()->json(['error' => "Missing chunk {$i}."], 422);
            }
        }

        // Assemble into a temp file
        $ext      = pathinfo($request->original_name, PATHINFO_EXTENSION);
        $tmpName  = Str::uuid() . ($ext ? '.' . $ext : '');
        $tmpPath  = storage_path('app/temp/' . $tmpName);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $out = fopen($tmpPath, 'wb');
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = $dir . '/chunk_' . $i;
            $in = fopen($chunkPath, 'rb');
            stream_copy_to_stream($in, $out);
            fclose($in);
        }
        fclose($out);

        // Quota check
        $fileSize = filesize($tmpPath);
        if (auth()->user()->wouldExceedQuota($fileSize)) {
            @unlink($tmpPath);
            $this->cleanChunkDir($dir);
            return response()->json(['error' => 'This upload would exceed your storage quota.'], 422);
        }

        // Duplicate detection
        $hash      = hash_file('sha256', $tmpPath);
        $duplicate = Resource::where('file_hash', $hash)->whereNull('deleted_at')->first();
        if ($duplicate) {
            @unlink($tmpPath);
            $this->cleanChunkDir($dir);
            return response()->json([
                'error' => "A document with identical content already exists: \"{$duplicate->title}\" (ID {$duplicate->id}).",
            ], 422);
        }

        // Move assembled file into final storage
        $storedName = $tmpName;
        $finalPath  = 'resources/' . $storedName;
        Storage::disk('local')->put($finalPath, file_get_contents($tmpPath));
        @unlink($tmpPath);
        $this->cleanChunkDir($dir);

        $resource = Resource::create([
            'title'             => $request->title,
            'description'       => $request->description,
            'original_filename' => $request->original_name,
            'stored_filename'   => $storedName,
            'file_path'         => $finalPath,
            'file_type'         => mime_content_type(Storage::disk('local')->path($finalPath)) ?: 'application/octet-stream',
            'file_size'         => $fileSize,
            'file_hash'         => $hash,
            'category_id'       => $request->category_id,
            'uploaded_by'       => auth()->id(),
            'status'            => $request->status,
        ]);

        if ($request->tags) {
            $resource->tags()->sync($request->tags);
        }

        DocumentVersion::create([
            'resource_id'     => $resource->id,
            'version_number'  => 1,
            'file_path'       => $finalPath,
            'stored_filename' => $storedName,
            'file_size'       => $fileSize,
            'file_hash'       => $hash,
            'change_note'     => 'Initial upload (chunked)',
            'uploaded_by'     => auth()->id(),
            'created_at'      => now(),
        ]);

        AuditLog::record('document.uploaded', $resource->id, [
            'title'   => $resource->title,
            'chunked' => true,
        ]);

        ExtractDocumentContent::dispatch($resource);
        $this->clearDocumentCaches();

        return response()->json([
            'message'     => "Document \"{$resource->title}\" uploaded.",
            'resource_id' => $resource->id,
            'redirect'    => route('admin.documents.index'),
        ]);
    }

    /**
     * Assemble chunks into a new document version.
     */
    public function assembleVersion(Request $request, Resource $resource)
    {
        $request->validate([
            'file_id'       => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]{8,64}$/'],
            'total_chunks'  => ['required', 'integer', 'min:1', 'max:200'],
            'original_name' => ['required', 'string', 'max:255'],
            'change_note'   => ['nullable', 'string', 'max:500'],
        ]);

        $dir         = $this->chunkDir . '/' . $request->file_id;
        $totalChunks = (int) $request->total_chunks;

        for ($i = 0; $i < $totalChunks; $i++) {
            if (!file_exists($dir . '/chunk_' . $i)) {
                return response()->json(['error' => "Missing chunk {$i}."], 422);
            }
        }

        $ext     = pathinfo($request->original_name, PATHINFO_EXTENSION);
        $tmpName = Str::uuid() . ($ext ? '.' . $ext : '');
        $tmpPath = storage_path('app/temp/' . $tmpName);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $out = fopen($tmpPath, 'wb');
        for ($i = 0; $i < $totalChunks; $i++) {
            $in = fopen($dir . '/chunk_' . $i, 'rb');
            stream_copy_to_stream($in, $out);
            fclose($in);
        }
        fclose($out);

        $fileSize   = filesize($tmpPath);
        $hash       = hash_file('sha256', $tmpPath);
        $storedName = $tmpName;
        $finalPath  = 'resources/' . $storedName;

        Storage::disk('local')->put($finalPath, file_get_contents($tmpPath));
        @unlink($tmpPath);
        $this->cleanChunkDir($dir);

        $nextVersion = ($resource->versions()->max('version_number') ?? 0) + 1;

        DocumentVersion::create([
            'resource_id'     => $resource->id,
            'version_number'  => $nextVersion,
            'file_path'       => $finalPath,
            'stored_filename' => $storedName,
            'file_size'       => $fileSize,
            'file_hash'       => $hash,
            'change_note'     => $request->change_note,
            'uploaded_by'     => auth()->id(),
            'created_at'      => now(),
        ]);

        $resource->update([
            'stored_filename'   => $storedName,
            'file_path'         => $finalPath,
            'file_size'         => $fileSize,
            'file_hash'         => $hash,
            'original_filename' => $request->original_name,
            'file_type'         => mime_content_type(Storage::disk('local')->path($finalPath)) ?: 'application/octet-stream',
        ]);

        AuditLog::record('document.version_uploaded', $resource->id, ['version' => $nextVersion]);
        ExtractDocumentContent::dispatch($resource);

        return response()->json([
            'message'  => "Version {$nextVersion} uploaded successfully.",
            'redirect' => route('admin.documents.edit', $resource),
        ]);
    }

    private function cleanChunkDir(string $dir): void
    {
        if (is_dir($dir)) {
            array_map('unlink', glob($dir . '/chunk_*'));
            @rmdir($dir);
        }
    }
}
