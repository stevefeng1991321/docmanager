<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestInvite extends Model
{
    protected $fillable = [
        'test_id',
        'candidate_name',
        'candidate_email',
        'token',
        'status',
        'started_at',
        'expires_at',
        'submitted_at',
        'total_score',
        'max_score',
        'graded_at',
        'graded_by',
        'created_by',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'expires_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at'    => 'datetime',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isExpired(): bool
    {
        return $this->status === 'started' && $this->expires_at && $this->expires_at->isPast();
    }
}
