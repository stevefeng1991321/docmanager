<?php

namespace App\Models;

use App\Enums\PlanPriority;
use App\Enums\PlanStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'plan_number', 'title', 'description', 'category',
        'department_id', 'project_id', 'owner_id',
        'priority', 'status',
        'start_date', 'due_date', 'completion_date',
        'estimated_hours', 'actual_hours', 'progress',
        'tags', 'notes',
    ];

    protected $casts = [
        'start_date'      => 'date',
        'due_date'        => 'date',
        'completion_date' => 'date',
        'tags'            => 'array',
        'estimated_hours' => 'float',
        'actual_hours'    => 'float',
        'status'          => PlanStatus::class,
        'priority'        => PlanPriority::class,
    ];

    // ── Helpers ───────────────────────────────────────────────────────────────

    public static function nextNumber(): string
    {
        $max = static::withTrashed()
            ->selectRaw("MAX(CAST(SUBSTRING(plan_number, 5) AS UNSIGNED)) as max_seq")
            ->value('max_seq');

        return 'PLN-' . str_pad((int) $max + 1, 5, '0', STR_PAD_LEFT);
    }

    public function updateProgress(): void
    {
        $total = $this->tasks()->count();
        if ($total === 0) return;

        $completed = $this->tasks()->where('status', 'completed')->count();
        $this->update(['progress' => (int) round($completed / $total * 100)]);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && !$this->status?->isTerminal();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status?->label() ?? '';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status?->color() ?? 'gray';
    }

    public function getPriorityColorAttribute(): string
    {
        return $this->priority?->color() ?? 'gray';
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function owner(): BelongsTo           { return $this->belongsTo(User::class, 'owner_id'); }
    public function department(): BelongsTo      { return $this->belongsTo(Department::class); }
    public function project(): BelongsTo         { return $this->belongsTo(Project::class); }
    public function tasks(): HasMany             { return $this->hasMany(PlanTask::class)->orderBy('sort_order'); }
    public function comments(): HasMany          { return $this->hasMany(PlanComment::class)->latest(); }
    public function attachments(): HasMany       { return $this->hasMany(PlanAttachment::class)->latest(); }
    public function employees(): BelongsToMany  {
        return $this->belongsToMany(Employee::class, 'plan_employees')->withTimestamps();
    }
}
