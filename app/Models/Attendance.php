<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id', 'date', 'status',
        'check_in_time', 'check_out_time', 'late_minutes',
        'notes', 'marked_by',
    ];

    protected $casts = [
        'date'   => 'date',
        'status' => AttendanceStatus::class,
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status?->label() ?? '';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status?->color() ?? 'gray';
    }

    public function getWorkDurationAttribute(): ?string
    {
        if (!$this->check_in_time || !$this->check_out_time) return null;

        $in  = \Carbon\Carbon::createFromTimeString($this->check_in_time);
        $out = \Carbon\Carbon::createFromTimeString($this->check_out_time);
        $mins = $in->diffInMinutes($out);

        return sprintf('%dh %02dm', intdiv($mins, 60), $mins % 60);
    }
}
