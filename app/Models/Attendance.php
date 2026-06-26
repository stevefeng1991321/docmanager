<?php

namespace App\Models;

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
        'date' => 'date',
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
        return match($this->status) {
            'present'  => 'Present',
            'absent'   => 'Absent',
            'late'     => 'Late',
            'on_leave' => 'On Leave',
            'holiday'  => 'Holiday',
            'half_day' => 'Half Day',
            default    => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'present'  => 'green',
            'absent'   => 'red',
            'late'     => 'yellow',
            'on_leave' => 'blue',
            'holiday'  => 'purple',
            'half_day' => 'orange',
            default    => 'gray',
        };
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
