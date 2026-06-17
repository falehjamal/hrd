<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PayrollEntryItem extends Model
{
    public const TYPE_EARNING = 'earning';

    public const TYPE_DEDUCTION = 'deduction';

    public const CATEGORY_BASIC_SALARY = 'basic_salary';

    public const CATEGORY_FIXED_ALLOWANCE = 'fixed_allowance';

    public const CATEGORY_EMPLOYEE_DEDUCTION = 'employee_deduction';

    public const CATEGORY_LOAN_INSTALLMENT = 'loan_installment';

    public const CATEGORY_OVERTIME = 'overtime';

    protected $fillable = [
        'payroll_entry_id',
        'type',
        'category',
        'label',
        'amount',
        'reference_type',
        'reference_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(PayrollEntry::class, 'payroll_entry_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
