<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TrendMedia extends Model
{
    protected $table = 'trend_media';

    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'type',
        'title',
        'file_path',
        'embed_url',
        'sort_order',
        'uploaded_by',
    ];

    public function mediable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isEmbedded(): bool
    {
        return $this->embed_url !== null;
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    public static function normalizeEmbedUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        // YouTube watch URL
        if (preg_match('/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        // YouTube short URL
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        // Vimeo (not already embed format)
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m) && !str_contains($url, 'player.vimeo.com')) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return $url;
    }
}
