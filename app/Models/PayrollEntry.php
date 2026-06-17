<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollEntry extends Model
{
    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'basic_salary',
        'fixed_allowance',
        'overtime_minutes',
        'overtime_pay',
        'total_earnings',
        'total_deductions',
        'net_salary',
        'is_skipped',
        'skip_reason',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'fixed_allowance' => 'decimal:2',
            'overtime_minutes' => 'integer',
            'overtime_pay' => 'decimal:2',
            'total_earnings' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'is_skipped' => 'boolean',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollEntryItem::class);
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(PayrollEntryItem::class)
            ->where('type', PayrollEntryItem::TYPE_EARNING);
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(PayrollEntryItem::class)
            ->where('type', PayrollEntryItem::TYPE_DEDUCTION);
    }

    public function overtimeRequests(): HasMany
    {
        return $this->hasMany(OvertimeRequest::class);
    }
}
