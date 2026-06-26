<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkReportAttachment extends Model
{
    protected $fillable = [
        'work_report_id',
        'title',
        'file_path',
        'uploaded_by',
    ];

    public function workReport(): BelongsTo
    {
        return $this->belongsTo(WorkReport::class);
    }

    public function isImage(): bool
    {
        $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
