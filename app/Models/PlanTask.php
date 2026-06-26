<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanTask extends Model
{
    protected $fillable = [
        'plan_id', 'title', 'description',
        'assigned_to', 'priority', 'status',
        'start_date', 'due_date', 'completed_at',
        'sort_order', 'notes',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    public function plan(): BelongsTo         { return $this->belongsTo(Plan::class); }
    public function assignedTo(): BelongsTo   { return $this->belongsTo(Employee::class, 'assigned_to'); }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && $this->status !== 'completed';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'     => 'gray',
            'in_progress' => 'blue',
            'completed'   => 'green',
            'cancelled'   => 'red',
            default       => 'gray',
        };
    }
}
