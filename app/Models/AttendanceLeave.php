<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLeave extends Model
{
    protected $fillable = [
        'employee_id', 'leave_type', 'start_date', 'end_date',
        'days_count', 'reason', 'status',
        'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'approved_at' => 'datetime',
        'days_count'  => 'float',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getLeaveTypeLabelAttribute(): string
    {
        return match($this->leave_type) {
            'annual'    => 'Annual Leave',
            'sick'      => 'Sick Leave',
            'personal'  => 'Personal Leave',
            'unpaid'    => 'Unpaid Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave',
            default     => ucfirst($this->leave_type),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'yellow',
            'approved'  => 'green',
            'rejected'  => 'red',
            'cancelled' => 'gray',
            default     => 'gray',
        };
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
