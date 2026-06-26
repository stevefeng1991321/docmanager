<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Plan;
use App\Models\PlanAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlanAttachmentController extends Controller
{
    public function store(Request $request, Plan $plan)
    {
        $request->validate([
            'file' => 'required|file|max:20480',
        ]);

        $file = $request->file('file');
        $path = $file->store('plan-attachments/' . $plan->id, 'public');

        $plan->attachments()->create([
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'file_size'     => $file->getSize(),
            'mime_type'     => $file->getMimeType(),
            'uploaded_by'   => auth()->id(),
        ]);

        AuditLog::record('plan.attachment_added', $plan->id, ['file' => $file->getClientOriginalName()]);

        return back()->with('message', 'File uploaded.');
    }

    public function download(Plan $plan, PlanAttachment $attachment)
    {
        abort_unless(Storage::disk('public')->exists($attachment->file_path), 404);

        return Storage::disk('public')->download($attachment->file_path, $attachment->original_name);
    }

    public function destroy(Plan $plan, PlanAttachment $attachment)
    {
        Storage::disk('public')->delete($attachment->file_path);
        AuditLog::record('plan.attachment_deleted', $plan->id, ['file' => $attachment->original_name]);
        $attachment->delete();

        return back()->with('message', 'Attachment deleted.');
    }
}
