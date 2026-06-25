<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    protected $fillable = [
        'title',
        'description',
        'time_limit_minutes',
        'status',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function problems(): BelongsToMany
    {
        return $this->belongsToMany(Problem::class, 'test_problems')
            ->withPivot(['order_index', 'points'])
            ->withTimestamps()
            ->orderBy('test_problems.order_index');
    }

    public function invites(): HasMany
    {
        return $this->hasMany(TestInvite::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
