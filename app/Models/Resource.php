<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Resource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'original_filename', 'stored_filename',
        'file_path', 'file_type', 'file_size', 'file_hash', 'content',
        'category_id', 'uploaded_by', 'status',
        'locked_by', 'locked_at', 'download_count', 'expires_at', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'locked_at'   => 'datetime',
            'expires_at'  => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function isStale(int $months): bool
    {
        $baseline = $this->reviewed_at ?? $this->updated_at;
        return $baseline !== null && $baseline->lt(now()->subMonths($months));
    }

    public function isLocked(): bool
    {
        return $this->locked_by !== null;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeSorted($query, string $sort)
    {
        return match($sort) {
            'name_asc'   => $query->orderBy('title'),
            'name_desc'  => $query->orderByDesc('title'),
            'size_desc'  => $query->orderByDesc('file_size'),
            'downloads'  => $query->orderByDesc('download_count'),
            'date_asc'   => $query->orderBy('created_at'),
            'date_desc'  => $query->orderByDesc('created_at'),
            default      => $query->orderByDesc('created_at'),
        };
    }

    public function getCurrentVersionAttribute(): int
    {
        return $this->versions()->max('version_number') ?? 1;
    }

    // ---------- relationships ----------

    public function uploader()       { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function locker()         { return $this->belongsTo(User::class, 'locked_by'); }
    public function category()       { return $this->belongsTo(Category::class); }
    public function versions()       { return $this->hasMany(DocumentVersion::class)->orderByDesc('version_number'); }
    public function tags()           { return $this->belongsToMany(Tag::class, 'resource_tags'); }
    public function accessLogs()     { return $this->hasMany(DocumentAccessLog::class); }
    public function favorites()      { return $this->hasMany(Favorite::class); }
    public function recentlyViewed() { return $this->morphMany(RecentlyViewed::class, 'viewable'); }
    public function readingLists()   { return $this->belongsToMany(ReadingList::class, 'reading_list_items')->withPivot('sort_order', 'added_at'); }
    public function bookmarks()      { return $this->hasMany(Bookmark::class); }
    public function ratings()        { return $this->hasMany(DocumentRating::class); }
    public function shares()         { return $this->hasMany(Share::class); }
    public function embeddings()     { return $this->hasMany(ResourceEmbedding::class); }
    public function downloadLogs()   { return $this->hasMany(DownloadLog::class); }

    public function averageRating(): ?float
    {
        $avg = $this->ratings()->avg('rating');
        return $avg !== null ? (float) $avg : null;
    }
}
