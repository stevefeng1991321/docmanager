<?php

namespace App\Models;

use App\Enums\LeaveRequestStatus;
use App\Enums\LeaveType;
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
        'leave_type'  => LeaveType::class,
        'status'      => LeaveRequestStatus::class,
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
        return $this->leave_type?->label() ?? '';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status?->color() ?? 'gray';
    }

    public function isPending(): bool
    {
        return $this->status === LeaveRequestStatus::Pending;
    }
}
