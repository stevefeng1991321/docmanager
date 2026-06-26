<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkReportComment extends Model
{
    protected $fillable = [
        'work_report_id',
        'user_id',
        'type',
        'body',
    ];

    public function workReport(): BelongsTo
    {
        return $this->belongsTo(WorkReport::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
