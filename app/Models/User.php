<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username', 'name', 'email', 'password',
        'role', 'status',
        'failed_login_attempts', 'locked_until',
        'two_factor_secret', 'two_factor_recovery_codes',
        'last_login_at', 'storage_quota_mb',
    ];

    protected $hidden = [
        'password', 'remember_token',
        'two_factor_secret', 'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'password'                  => 'hashed',
            'locked_until'              => 'datetime',
            'last_login_at'             => 'datetime',
            'two_factor_recovery_codes' => 'array',
        ];
    }

    // ---------- helpers ----------

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isEditor(): bool   { return $this->role === 'editor'; }
    public function isViewer(): bool   { return $this->role === 'viewer'; }
    public function isActive(): bool   { return $this->status === 'active'; }
    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isLocked(): bool   { return $this->locked_until && $this->locked_until->isFuture(); }

    // ---------- storage quota ----------

    public function storageUsedBytes(): int
    {
        return (int) $this->resources()->whereNull('deleted_at')->sum('file_size');
    }

    public function storageQuotaBytes(): ?int
    {
        return $this->storage_quota_mb ? $this->storage_quota_mb * 1024 * 1024 : null;
    }

    public function wouldExceedQuota(int $incomingBytes): bool
    {
        $quota = $this->storageQuotaBytes();
        if ($quota === null) return false;
        return ($this->storageUsedBytes() + $incomingBytes) > $quota;
    }

    // ---------- relationships ----------

    public function resources()        { return $this->hasMany(Resource::class, 'uploaded_by'); }
    public function favorites()        { return $this->hasMany(Favorite::class); }
    public function recentlyViewed()   { return $this->hasMany(RecentlyViewed::class); }
    public function savedSearches()    { return $this->hasMany(SavedSearch::class); }
    public function readingLists()     { return $this->hasMany(ReadingList::class); }
    public function bookmarks()        { return $this->hasMany(Bookmark::class); }
    public function ratings()          { return $this->hasMany(DocumentRating::class); }
    public function notifications()    { return $this->hasMany(Notification::class); }
    public function preferences()      { return $this->hasOne(UserPreference::class); }
    public function auditLogs()        { return $this->hasMany(AuditLog::class); }
    public function activityLogs()     { return $this->hasMany(ActivityLog::class); }
}
