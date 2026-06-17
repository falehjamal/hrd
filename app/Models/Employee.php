<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    public const GENDER_LABELS = [
        'male' => 'Laki-laki',
        'female' => 'Perempuan',
    ];

    protected $fillable = [
        'employee_code',
        'name',
        'email',
        'phone',
        'photo_path',
        'national_id',
        'gender',
        'birth_date',
        'address',
        'position_id',
        'organizational_unit_id',
        'manager_id',
        'shift_id',
        'join_date',
        'status',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'birth_date' => 'date',
        ];
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        return route('employees.photo', $this);
    }

    public function getGenderLabelAttribute(): ?string
    {
        if (! $this->gender) {
            return null;
        }

        return self::GENDER_LABELS[$this->gender] ?? null;
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(self::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(self::class, 'manager_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(EmployeeSalary::class);
    }

    public function activeSalary(): HasOne
    {
        return $this->hasOne(EmployeeSalary::class)->where('is_active', true);
    }

    public function weeklyShifts(): HasMany
    {
        return $this->hasMany(EmployeeWeeklyShift::class);
    }

    public function shiftOverrides(): HasMany
    {
        return $this->hasMany(EmployeeShiftOverride::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function overtimeRequests(): HasMany
    {
        return $this->hasMany(OvertimeRequest::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(EmployeeLeaveBalance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(EmployeeDeduction::class);
    }

    public function activeDeductions(): HasMany
    {
        return $this->hasMany(EmployeeDeduction::class)->where('is_active', true);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(EmployeeLoan::class);
    }

    public function activeLoans(): HasMany
    {
        return $this->hasMany(EmployeeLoan::class)->where('status', EmployeeLoan::STATUS_ACTIVE);
    }

    public function payrollEntries(): HasMany
    {
        return $this->hasMany(PayrollEntry::class);
    }

    public function getTotalActiveDeductionsAttribute(): float
    {
        return (float) $this->activeDeductions()->sum('amount');
    }

    public function getTotalLoanRemainingAttribute(): float
    {
        return (float) $this->activeLoans()
            ->get()
            ->sum(fn (EmployeeLoan $loan) => $loan->remaining_amount);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
