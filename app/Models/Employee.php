<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_code', 'user_id', 'photo_path',
        'full_name', 'email', 'phone', 'date_of_birth', 'gender', 'address',
        'emergency_contact_name', 'emergency_contact_phone', 'nationality',
        'marital_status', 'employment_status',
        'department_id', 'position_id', 'manager_id',
        'date_of_joining', 'employment_type', 'salary', 'work_location', 'office_branch',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'   => 'date',
            'date_of_joining' => 'date',
            'salary'          => 'decimal:2',
        ];
    }

    // ---------- helpers ----------

    public function isActive(): bool     { return $this->employment_status === 'active'; }
    public function isInactive(): bool   { return $this->employment_status === 'inactive'; }
    public function isResigned(): bool   { return $this->employment_status === 'resigned'; }
    public function isTerminated(): bool { return $this->employment_status === 'terminated'; }

    public function photoUrl(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    public static function nextCode(): string
    {
        $max = static::withTrashed()
            ->selectRaw("MAX(CAST(SUBSTRING(employee_code, 5) AS UNSIGNED)) as max_seq")
            ->value('max_seq');

        return 'EMP-' . str_pad((int) $max + 1, 5, '0', STR_PAD_LEFT);
    }

    // ---------- relationships ----------

    public function user(): BelongsTo         { return $this->belongsTo(User::class); }
    public function department(): BelongsTo   { return $this->belongsTo(Department::class); }
    public function position(): BelongsTo     { return $this->belongsTo(Position::class); }
    public function manager(): BelongsTo      { return $this->belongsTo(Employee::class, 'manager_id'); }
    public function subordinates(): HasMany   { return $this->hasMany(Employee::class, 'manager_id'); }
    public function documents(): HasMany      { return $this->hasMany(EmployeeDocument::class); }
    public function workReports(): HasMany    { return $this->hasMany(WorkReport::class); }
    public function attendances(): HasMany    { return $this->hasMany(Attendance::class); }
    public function leaves(): HasMany         { return $this->hasMany(AttendanceLeave::class); }
}
