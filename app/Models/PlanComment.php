<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanComment extends Model
{
    protected $fillable = ['plan_id', 'user_id', 'body'];

    public function plan(): BelongsTo { return $this->belongsTo(Plan::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
