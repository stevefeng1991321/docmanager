<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\DatabaseBackupService;
use Illuminate\Http\Request;

class DatabaseBackupController extends Controller
{
    public function __construct(private DatabaseBackupService $backups) {}

    public function index()
    {
        $backups = $this->backups->list();
        return view('admin.backup.index', compact('backups'));
    }

    public function store()
    {
        try {
            $filename = $this->backups->create();
            AuditLog::record('database.backup.created', null, ['filename' => $filename]);
            return back()->with('message', "Backup created: {$filename}");
        } catch (\Throwable $e) {
            return back()->with(['message' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function download(string $filename)
    {
        try {
            $path = $this->backups->safePath($filename);
            AuditLog::record('database.backup.downloaded', null, ['filename' => $filename]);
            return response()->download($path, $filename);
        } catch (\InvalidArgumentException $e) {
            abort(404);
        }
    }

    public function destroy(string $filename)
    {
        try {
            $this->backups->delete($filename);
            AuditLog::record('database.backup.deleted', null, ['filename' => $filename]);
            return back()->with('message', 'Backup deleted.');
        } catch (\Throwable $e) {
            return back()->with(['message' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function restoreFromFile(string $filename)
    {
        try {
            $path = $this->backups->safePath($filename);
            $this->backups->restore($path);
            AuditLog::record('database.backup.restored', null, ['filename' => $filename, 'source' => 'existing']);
            return back()->with('message', 'Database restored from backup successfully.');
        } catch (\Throwable $e) {
            return back()->with(['message' => $e->getMessage(), 'status' => 'error']);
        }
    }

    public function restoreFromUpload(Request $request)
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'max:204800'],
        ]);

        $file = $request->file('backup_file');

        if ($file->getClientOriginalExtension() !== 'sql') {
            return back()->with(['message' => 'Only .sql files are accepted.', 'status' => 'error']);
        }

        $tmpPath = $file->getRealPath();

        try {
            $this->backups->restore($tmpPath);
            AuditLog::record('database.backup.restored', null, [
                'filename' => $file->getClientOriginalName(),
                'source'   => 'upload',
            ]);
            return back()->with('message', 'Database restored from uploaded file successfully.');
        } catch (\Throwable $e) {
            return back()->with(['message' => $e->getMessage(), 'status' => 'error']);
        }
    }
}
