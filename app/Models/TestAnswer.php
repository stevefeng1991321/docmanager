<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestAnswer extends Model
{
    protected $fillable = [
        'test_invite_id',
        'problem_id',
        'code',
        'score',
        'feedback',
    ];

    public function invite(): BelongsTo
    {
        return $this->belongsTo(TestInvite::class, 'test_invite_id');
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }
}
