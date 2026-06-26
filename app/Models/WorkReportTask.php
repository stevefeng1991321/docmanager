<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkReportTask extends Model
{
    protected $fillable = [
        'work_report_id',
        'title',
        'status',
        'priority',
        'completion_percent',
        'time_spent_hours',
        'order_index',
    ];

    public function workReport(): BelongsTo
    {
        return $this->belongsTo(WorkReport::class);
    }
}
