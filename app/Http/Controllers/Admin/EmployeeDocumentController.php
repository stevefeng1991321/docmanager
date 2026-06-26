<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeDocumentController extends Controller
{
    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'type'  => ['required', 'in:contract,identification,certificate,performance_review,other'],
            'title' => ['required', 'string', 'max:255'],
            'file'  => ['required', 'file', 'max:10240'],
        ]);

        $file       = $request->file('file');
        $storedName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path       = $file->storeAs("employees/{$employee->id}/documents", $storedName, 'local');

        $document = $employee->documents()->create([
            'type'        => $validated['type'],
            'title'       => $validated['title'],
            'file_path'   => $path,
            'uploaded_by' => auth()->id(),
        ]);

        AuditLog::record('employee_document.uploaded', $employee->id, ['title' => $document->title, 'type' => $document->type]);

        return back()->with('message', "Document \"{$document->title}\" uploaded.");
    }

    public function download(Employee $employee, EmployeeDocument $document)
    {
        abort_unless($document->employee_id === $employee->id, 404);
        abort_if(!Storage::disk('local')->exists($document->file_path), 404);

        AuditLog::record('employee_document.downloaded', $employee->id, ['title' => $document->title]);

        return Storage::disk('local')->download($document->file_path, $document->title);
    }

    public function destroy(Employee $employee, EmployeeDocument $document)
    {
        abort_unless($document->employee_id === $employee->id, 404);

        Storage::disk('local')->delete($document->file_path);
        $title = $document->title;
        $document->delete();

        AuditLog::record('employee_document.deleted', $employee->id, ['title' => $title]);

        return back()->with('message', "Document \"{$title}\" deleted.");
    }
}
