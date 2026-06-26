<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id', 'title', 'type', 'report_date', 'project_id', 'client_name',
        'tasks_completed', 'task_descriptions', 'challenges', 'solutions', 'notes',
        'work_hours', 'overall_progress',
        'status', 'submitted_at', 'reviewed_at', 'reviewed_by',
    ];

    protected function casts(): array
    {
        return [
            'report_date'  => 'date',
            'work_hours'   => 'decimal:2',
            'submitted_at' => 'datetime',
            'reviewed_at'  => 'datetime',
        ];
    }

    // ---------- status helpers ----------

    public function isDraft(): bool       { return $this->status === 'draft'; }
    public function isSubmitted(): bool   { return $this->status === 'submitted'; }
    public function isUnderReview(): bool { return $this->status === 'under_review'; }
    public function isApproved(): bool    { return $this->status === 'approved'; }
    public function isRejected(): bool    { return $this->status === 'rejected'; }

    // ---------- authorization helpers ----------

    public function isOwnedBy(User $user): bool
    {
        return $this->employee->user_id !== null && $this->employee->user_id === $user->id;
    }

    public function canBeEditedBy(User $user): bool
    {
        return $this->isOwnedBy($user) && in_array($this->status, ['draft', 'rejected']);
    }

    public function canBeReviewedBy(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($this->status === 'draft') {
            return false;
        }

        $employee = $user->employee;

        return $employee !== null && $this->employee->manager_id === $employee->id;
    }

    public function canBeViewedBy(User $user): bool
    {
        return $this->isOwnedBy($user) || $this->canBeReviewedBy($user);
    }

    // ---------- relationships ----------

    public function employee(): BelongsTo    { return $this->belongsTo(Employee::class); }
    public function project(): BelongsTo     { return $this->belongsTo(Project::class); }
    public function reviewedBy(): BelongsTo  { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function tasks(): HasMany         { return $this->hasMany(WorkReportTask::class)->orderBy('order_index'); }
    public function comments(): HasMany      { return $this->hasMany(WorkReportComment::class)->orderBy('created_at'); }
    public function attachments(): HasMany   { return $this->hasMany(WorkReportAttachment::class); }
}
