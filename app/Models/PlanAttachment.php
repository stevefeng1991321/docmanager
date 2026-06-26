<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PlanAttachment extends Model
{
    protected $fillable = [
        'plan_id', 'original_name', 'file_path',
        'file_size', 'mime_type', 'uploaded_by',
    ];

    public function plan(): BelongsTo       { return $this->belongsTo(Plan::class); }
    public function uploadedBy(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024)       return $bytes . ' B';
        if ($bytes < 1048576)    return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function getIconAttribute(): string
    {
        $mime = $this->mime_type ?? '';
        if (str_starts_with($mime, 'image/'))               return '🖼️';
        if ($mime === 'application/pdf')                    return '📄';
        if (str_contains($mime, 'spreadsheet') || str_contains($mime, 'excel')) return '📊';
        if (str_contains($mime, 'presentation') || str_contains($mime, 'powerpoint')) return '📑';
        if (str_contains($mime, 'word') || str_contains($mime, 'document')) return '📝';
        return '📎';
    }
}
