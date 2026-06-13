<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDeduction extends Model
{
    protected $fillable = [
        'employee_id',
        'deduction_type_id',
        'amount',
        'effective_date',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function deductionType(): BelongsTo
    {
        return $this->belongsTo(DeductionType::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function deactivateOthersForEmployeeAndType(int $employeeId, int $deductionTypeId, ?int $exceptId = null): void
    {
        static::query()
            ->where('employee_id', $employeeId)
            ->where('deduction_type_id', $deductionTypeId)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->update(['is_active' => false]);
    }
}
